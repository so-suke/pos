const $numInputModal = document.getElementById('numInputModal');

//スキャンした時の初期販売数は1。
const SALES_NUM_INIT = 1
//画面切替用定数
const DISPLAY_MODE = {
  NORMAL: 'normal',
  SIMPLE: 'simple',
  CANCEL: 'cancel',
}
//簡易販売画面にて、数値入力モーダルで使用する。(乗算、数値変更)の内、どれを行うかを判定用に使用。
const WHAT_SHOULD_ACTION = {
  MULTIPLICATION: 'multiplication',
  UPDATE: 'update',
}

var app = new Vue({
  el: '#app',
  mixins: [simpleSalesMixin],
  data: {
    row_num: 0,
    total: 0,
    DISPLAY_MODE,
    display_mode: DISPLAY_MODE.NORMAL,
    is_accounted: false,
    normal: {
      sales_items: [],
      total: 0,
    },
    cancel: {
      choosed_idx_in_sales_items: null,
      choosed_radio: null,
    },
  },
  methods: {
    _isDiscount: function({ item }) {
      return item instanceof Discount;
    },
    _decideToSellIfHasDiscount: function({ item }) { //販売したことにする、もし、値下対象商品なら。
      if (this._hasDiscount({ item })) {
        this.normal.sales_items.push(item.discount);
      }
    },
    _initSalesItems: function() {
      this.normal.sales_items = [];
      this.simple.sales_items = [];
    },
    _initSimpleSales: function() {
      //簡易販売商品の販売数の初期化。
			for (const category_name in simple_sales_all_items) {
				const simple_sales_items = simple_sales_all_items[category_name];
				simple_sales_items.forEach((item) => {
					item.initSalesNum();
				})
			}
    },
    _initAccountSystem: function() {
      this.$refs.deposit_price.value = '';
      this.$refs.change_price.value = '';
    },
    _initAccountedIfNeeded() {
      if (this.is_accounted === true) {
				this._initSalesItems();
        this._initAccountSystem();
        this.is_accounted = false;
      }
    },
    to_simple: function(event) {
      this._initAccountedIfNeeded();
      const small_category_name = event.target.dataset.small_category_name
      this.simple.sales_items = simple_sales_all_items[small_category_name];
      this.simple.sales_items.forEach(item => {
        item.sales_num.tmp = item.sales_num.confirmed
      });
      //もし、簡易販売商品に販売数があるものがあるならば、販売数変更ボタンをクリック可能にする。
      const has_sales_num = this.simple.sales_items.some((item) => {
        return item.sales_num.confirmed > 0;
      });
      if (has_sales_num === true) {
        this.$refs.simple_update_btn.classList.remove('disabled');
      }
      this.display_mode = DISPLAY_MODE.SIMPLE
    },
    scan: function() {
      const barcode = this.$refs.barcode.value
      if (barcode === '') {
        alert('バーコードを入力して下さい。');
        return;
      }
      this._initAccountedIfNeeded();
      const item_master = all_item_masters[barcode];
      const sales_item = new SalesItem({
        item_master,
        sales_num: SALES_NUM_INIT,
      });
      this.normal.sales_items.push(sales_item);
      this._decideToSellIfHasDiscount({ item: sales_item });
    },
    multiplication: function() {
      if (this.normal.sales_items.length === 0) {
        alert('商品をスキャンして下さい。');
        return;
      }
      let item_last_idx = this.normal.sales_items.length - 1;
      if (this._isDiscount({ item: this.normal.sales_items[item_last_idx] })) {
        item_last_idx -= 1;
      }
      const last_item = this.normal.sales_items[item_last_idx];
      if (last_item.isCancelItem() === true) {
        alert('取消商品は乗算できません。');
        return;
      }
      if (last_item.is_from_simple === true) {
        alert('簡易販売商品は乗算できません。');
        return;
      }
      if (last_item.sales_num === SALES_NUM_INIT) {
        const multi_val = parseInt(this.$refs.multi_input.value)
        last_item.multiplication_sales_num(multi_val);
      }
    },
    to_cancel_display: function() {
      this.display_mode = this.DISPLAY_MODE.CANCEL
    },
    //会計処理
    account: function() {
      const deposit_num = this.$refs.deposit_num.value;
      if (deposit_num === '') {
        alert('お預かり金額を入力して下さい。');
        return;
      } else if (deposit_num < this.normal.total) {
        alert('お預かり金額が足りません。');
        return;
      }

      const params = new URLSearchParams();
			//販売バーコードの設定
      this.normal.sales_items.filter((item) => {
          return (item instanceof SalesItem) === true
        }).map((item) => item.barcode)
        .forEach((barcode) => {
          params.append('sales_barcodes[]', barcode);
        });

      axios.post('/pos/public/ajax_q/account', params)
        .then(response => {
          // console.log(response.data)
          this.$refs.deposit_num.value = '';
          this.$refs.deposit_price.value = deposit_num;
          this.$refs.change_price.value = deposit_num - this.normal.total;
          this.is_accounted = true;
					this._initSimpleSales();
        })
        .catch(e => {
          console.log(e)
        })
    },
    _cancelSelectedForCancelRows: function() {
      //選択時につく、背景色をなくす。
      this.$refs.cancel_rows.forEach(($row) => {
        $row.classList.remove('bg-warning')
      })
    },
    _initSelectedForCancelRows: function() {
      //取消画面で選択されている商品のidxを無しにする。
      this.cancel.choosed_idx_in_sales_items = null;
      this.cancel.choosed_radio.checked = '';

      //選択時につく、背景色をなくす。
      this._cancelSelectedForCancelRows();
    },
    choose_cancel_item: function(event) {
      //選択前に全て、背景色をなくす。（選択できるのは、一つまでとするため）
      this.$refs.cancel_rows.forEach(($row) => {
        $row.classList.remove('bg-warning')
      });
      this._cancelSelectedForCancelRows();
      const radio = event.target;
      this.cancel.choosed_radio = radio; //後でcheckedを消すために保存。
      this.cancel.choosed_idx_in_sales_items = radio.dataset.idx_in_sales_items
      const item = this.normal.sales_items[this.cancel.choosed_idx_in_sales_items];
      const $ancestor = radio.closest('.grid-cancel-0')
      $ancestor.classList.add('bg-warning')
      if (item.discount !== null) {
        $ancestor.nextElementSibling.classList.add('bg-warning');
      }
      //確定ボタンをクリック可能にする。
      this.$refs.sales_cancel_ok.classList.remove('mydisabled');
    },
    sales_cancel_ok: function() {
      //該当商品取消。（該当商品の販売数をマイナスにしたものを購入したことにする。）
      //該当商品取得
      if (this.cancel.choosed_idx_in_sales_items === null) {
        return;
      }
      const canceled_item = this.normal.sales_items[this.cancel.choosed_idx_in_sales_items];
      //もし、該当商品が簡易販売経由のものならば、それの販売数を変更する。
      if (canceled_item.is_from_simple === true) {
        // もし、取消しようとしている販売数が簡易販売の販売数を超えているならばエラーにする。
        // 簡易販売商品に対してカテゴリ名と
        const simple_sales_items = simple_sales_all_items[canceled_item.small_category_name]
        // barcodeから絞り込み。
        const simple_item = simple_sales_items.find((item) => item.barcode === canceled_item.barcode);
        //取消販売数が簡易販売数を超えているか?
        if (canceled_item.sales_num > simple_item.sales_num.confirmed) {
          alert('簡易販売数を超えた取消は出来ません。');
          this._initSelectedForCancelRows();
          return;
        } else {
          simple_item.sales_num.confirmed -= canceled_item.sales_num
        }
      }
      //取消処理開始
      canceled_item.is_canceled = true;
      //マイナス商品を購入したことにして、商品を取消したことにする(重複取消防止のため。)
      const item_master = all_item_masters[canceled_item.barcode];
      const cancel_item = new SalesItem({ item_master, sales_num: canceled_item.sales_num * -1, is_canceled: true })
      //販売配列に含める
      this.normal.sales_items.push(cancel_item);
      this._decideToSellIfHasDiscount({ item: cancel_item });
      this._cancelDisplayToNormal();
    },
    _cancelDisplayToNormal: function() {
      if (this.cancel.choosed_idx_in_sales_items !== null && this.cancel.choosed_radio !== null) {
        this._initSelectedForCancelRows();
      }
      //確定ボタンをクリック不可にする。
      this.$refs.sales_cancel_ok.classList.add('mydisabled');

      //通常画面に戻る。
      this.display_mode = DISPLAY_MODE.NORMAL;
    },
    sales_cancel_cancel: function() {
      this._cancelDisplayToNormal();
    },
  },
  computed: {
    get_total: function() {
      //this.normal.totalは会計時使用するため、ひとまず代入する形にしておいてください。
      this.normal.total = this.normal.sales_items
        .filter((item) => {
          return item.name !== '値下'
        })
        .reduce((acc, item) => {
          let added = item.getSubTotal();
          if (item.hasDiscount() === true) {
            added -= item.discount.getSubTotal();
          }
          return acc + added;
        }, 0);

      return this.normal.total;
    },
  }
});
