class SimpleDiscount {
  constructor({ price, sales_num }) {
    this.price = price;
    this.name = '値下';
    this.sales_num = {
      confirmed: 0,
      tmp: 0,
    };
  }

  multiplication_sales_num(num) {
    this.sales_num.tmp *= num
  }

  update_sales_num(num) {
    this.sales_num.tmp = num
  }

  getSubTotal() {
    return this.price * this.sales_num.tmp
  }
}

//簡易販売画面に表示される商品を表す。主に販売数の確定分と一時的なものを区別して数えるために使用。
class SimpleSalesItem {
  constructor({ item_master }) {
    this.barcode = item_master.barcode;
    this.name = item_master.name;
    this.small_category_name = item_master.small_category_name;
    this.price = item_master.price;
		this.img_name = item_master.img_name;
		this.has_sales_num = false;
    if (item_master.discount_amt > 0) {
      this.discount = new SimpleDiscount({ price: item_master.discount_amt });
    } else {
      this.discount = null;
    }
    this.sales_num = {
      confirmed: 0,
      tmp: 0,
    }
  }

	initSalesNum() {
		this.sales_num.confirmed = 0;
		this.sales_num.tmp = 0;
	}

	hasSalesNumTMP() {
		return this.sales_num.tmp > 0;
	}

  hasDiscount() {
    return this.discount !== null;
  }

  isInitSales() {
    return this.sales_num.tmp === 1;
  }

  tmp_sales_by_click() {
    this.sales_num.tmp += 1;
    if (this.discount !== null) {
      this.discount.sales_num.tmp += 1;
    }
  }

  multiplication_sales_num(num) {
    this.sales_num.tmp *= num
    if (this.discount !== null) {
      this.discount.sales_num.tmp *= num;
    }
  }

  update_sales_num(num) {
    this.sales_num.tmp = num;
    if (this.discount !== null) {
      this.discount.sales_num.tmp = num;
    }
  }

  getSubTotal() {
    return this.price * this.sales_num.tmp
  }
}

//簡易販売画面で読み込まれる商品群の作成。
const simple_sales_all_items = simple_item_masters.reduce((acc, item_master) => {
  if (acc.hasOwnProperty(item_master.small_category_name) !== true) {
    acc[item_master.small_category_name] = []
  }
  acc[item_master.small_category_name].push(new SimpleSalesItem({ item_master }))
  return acc
}, {});

var simpleSalesMixin = {
  data: {
    simple: {
      sales_items: [],
      for_update_tmp_sales_items: [],
      last_selected_item: null, //最後に選択された初期販売(販売数が1)商品
      total: 0,
      is_sales_updating: false, //数値変更を行うときに使用。
      num_input_modal: {
        what_should_action: '',
      },
    },
  },
  methods: {
    _hasDiscount: function({ item }) {
      return item.discount !== null;
    },
    simple_sales: function(item) {
      //販売数操作アクションのため取っておく。
      this.simple.last_selected_item = item;
      //販売数更新系ボタン押下後は、通常販売ではなく、対象商品選択となる。
      if (this.simple.is_sales_updating === true) {
        this.simple.num_input_modal.what_should_action = WHAT_SHOULD_ACTION.UPDATE
        this.$refs.numInputModal.show();
        return;
      }
      item.tmp_sales_by_click();
      if (item.isInitSales()) {
        //商品の初回クリック時のみ
        this.$refs.simple_multiplication_btn.classList.remove('disabled')
      } else {
        this.$refs.simple_multiplication_btn.classList.add('disabled')
      }
      this.$refs.simple_update_btn.classList.remove('disabled')
    },
    simple_confirm: function() {
      //簡易販売商品確定分(販売数(確定分と一時的)の差から確定販売数を計算。)
      const simple_sales_items_confirmed = this.simple.sales_items.filter((item) => {
        return item.sales_num.confirmed !== item.sales_num.tmp
      }).reduce((acc, item) => {
        //差がマイナスの場合は、取消商品として、通常画面では表示される。
        const sales_num_diff = item.sales_num.tmp - item.sales_num.confirmed
        const item_master = all_item_masters[item.barcode];
        const sales_item = new SalesItem({ item_master, sales_num: sales_num_diff, is_from_simple: true });
        acc.push(sales_item);
        if (this._hasDiscount({ item: sales_item })) {
          acc.push(sales_item.discount);
        }
        return acc
      }, [])
      //通常販売に含める。
      this.normal.sales_items = this.normal.sales_items.concat(simple_sales_items_confirmed)
      this.simple.sales_items.forEach((item) => {
        return item.sales_num.confirmed = item.sales_num.tmp
      });

      this.display_mode = DISPLAY_MODE.NORMAL
      this._simple_option_btn_disabled();
    },
    simple_cancel: function() {
      this.display_mode = DISPLAY_MODE.NORMAL
      this._simple_option_btn_disabled();
    },
    num_input_modal_hide: function() {
      this.simple.last_selected_item = null
      if (this.simple.num_input_modal.what_should_action === WHAT_SHOULD_ACTION.UPDATE) {
        this.simple.sales_items = this.simple.for_update_tmp_sales_items
        this.simple.is_sales_updating = false
      }
      this.simple.num_input_modal.what_should_action = ''
    },
    _simple_option_btn_disabled: function() {
      this.$refs.simple_multiplication_btn.classList.add('disabled');
      this.$refs.simple_update_btn.classList.add('disabled');
    },
    prepare_simple_multiplication: function() { //行うべきアクションを乗算として, 数値入力モーダルを開く。
      this.simple.num_input_modal.what_should_action = WHAT_SHOULD_ACTION.MULTIPLICATION
      this.$refs.numInputModal.show();
    },
    prepare_simple_update: function() { //行うべきアクションを数値変更として, 数値入力モーダルを開く。
      //乗算btn、クリック不可にする。
      this.$refs.simple_multiplication_btn.classList.add('disabled');
      //商品ボタン押下時、数値入力モーダルを表示させるため。
      this.simple.is_sales_updating = true
      //販売数更新操作による商品表示切替のため一時保存。
      this.simple.for_update_tmp_sales_items = this.simple.sales_items
      //販売数が0より高いものを抽出して表示。
      // this.simple.sales_items = this.simple.sales_items.filter((item) => item.sales_num.tmp > 0)
      //販売数が0より高いものには、フラグを立てる。
      // this.simple.sales_items.filter((item) => item.sales_num.tmp > 0)
      //   .forEach((item) => item.has_sales_num = true);
    },
    num_input_modal_ok: function() {
      //最後に選択された商品に対して、販売数の「乗算」または「変更」を行う。
      const num = parseInt(this.$refs.simple_action_num_input.value)
      if (this.simple.num_input_modal.what_should_action === WHAT_SHOULD_ACTION.MULTIPLICATION) {
        this.simple.last_selected_item.multiplication_sales_num(num)
      } else if (this.simple.num_input_modal.what_should_action === WHAT_SHOULD_ACTION.UPDATE) {
        this.simple.last_selected_item.update_sales_num(num)
        if (num === 0) {
          const is_every_sales_none = this.simple.for_update_tmp_sales_items.every((item) => {
            return item.sales_num.tmp === 0;
          });
          if (is_every_sales_none) {
            this.$refs.simple_update_btn.classList.add('disabled');
          }
        }
      }
      this.$refs.numInputModal.hide()
    },
    num_input_modal_cancel: function() {
      this.$refs.numInputModal.hide()
    },
  },
  computed: {
    get_total_simple: function() {
      //簡易販売商品の合計金額を通常販売商品の合計額に足して、返す。
      //商品が値下げを持っていない場合は、商品価格の小計のみ。
      //商品が値下げを持っている場合は、値下げ分のものも考慮する。
      return this.simple.sales_items
        .reduce((acc, item) => {
          let added = item.getSubTotal();
          if (item.hasDiscount === true) {
            added -= item.discount.getSubTotal();
          }
          return acc + added;
        }, this.normal.total);
    },
  }
}
