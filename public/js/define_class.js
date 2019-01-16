class Discount {
  constructor({ price, sales_num }) {
    this.price = price;
    this.name = '値下';
    this.sales_num = sales_num;
  }

  multiplication_sales_num(num) {
    this.sales_num *= num
  }

  update_sales_num(num) {
    this.sales_num = num
  }

  getSubTotal() {
    return this.price * this.sales_num
  }
}

//通常画面に表示される商品を表す。
class SalesItem {
  constructor({ item_master, sales_num, is_canceled = false, is_from_simple = false }) {
    this.barcode = item_master.barcode;
    this.name = item_master.name
    this.small_category_name = item_master.small_category_name
    this.price = item_master.price
    if (item_master.discount_amt > 0) {
      this.discount = new Discount({ price: item_master.discount_amt, sales_num });
    } else {
      this.discount = null;
    }
    this.sales_num = sales_num
    this.is_canceled = is_canceled; //重複取消防止のため。
    this.is_from_simple = is_from_simple
  }

	isCancelItem() {
		return this.sales_num < 0;
	}

  hasDiscount() {
    return this.discount !== null;
  }

  multiplication_sales_num(num) {
    this.sales_num *= num
    if (this.discount instanceof Discount) {
      this.discount.multiplication_sales_num(num);
    }
  }

  update_sales_num(num) {
    this.sales_num = num
    if (this.discount instanceof Discount) {
      this.discount.update_sales_num(num);
    }
  }

  getSubTotal() {
    return this.price * this.sales_num
  }
}
