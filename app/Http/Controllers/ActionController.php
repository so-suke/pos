<?php

namespace App\Http\Controllers;
use App\Item;
use App\Sale;
use App\SalesDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ActionController extends Controller {

  //販売情報の初期データ作成用
  public function makeSalesDetails() {
    $now_fmt = Carbon::now()->format('Y-m-d H:i:s');
    //どのくらいの日数分のデータを作るか？
    $howmuch_date_num = (7 * 4) + 20;
    //本日日付から作る日数分、前日に戻った日付を作成する。販売日付の元になる日付。
    //subDaysの引数をマイナス1しないと今日が含まれない。Carbon::parseしているのは、今日日付の零時を取得したいからです。
    $base_sales_at = Carbon::parse(Carbon::now()->format('Y-m-d'))->subDays($howmuch_date_num - 1);
    //販売する商品barcodeを選ぶ
    $sales_barcodes = [1, 3, 10, 19, 22, 24, 26, 28, 32];
    //商品barcodeを商品に変換。
    $sales_items = [];
    foreach ($sales_barcodes as $key => $sales_barcode) {
      $item = Item::find($sales_barcode);
      $sales_items[] = $item;
    }

    //ここまでで販売商品の完成。

    // dd($sales_items);

    //販売商品を元に、販売明細の途中まで作成、販売商品インサートデータを作成。最後に販売明細を完成させる。
    $branch_office_id = 1;
    $staff_id = 1;
    $init_deposit_price = 1000;
    //どのくらいの時間数分、作成するか？
    $howmuch_hour_cnt = 3;
    for ($day_i = 0; $day_i < $howmuch_date_num; $day_i++) {
      for ($hour_i = 0; $hour_i < $howmuch_hour_cnt; $hour_i++) {
        //先に販売明細の作成。
        $sales_detail = new SalesDetail;
        $sales_detail->branch_office_id = $branch_office_id;
        $sales_detail->staff_id = $staff_id;
        $sales_detail->deposit_price = $init_deposit_price;
        $sales_detail->saled_at = $base_sales_at->format('Y-m-d H:i:s');
        //販売明細の合計系は、計算する。
        $total_sales_num = 0;
        $beforeDis_total_price = 0;
        $total_dis_price = 0;
        foreach ($sales_items as $key => $sales_item) {
          $sales_item->sales_num = rand(1, 3);
          $total_sales_num += $sales_item->sales_num;
          $beforeDis_total_price += $sales_item->price * $sales_item->sales_num;
          $total_dis_price += $sales_item->discount_amt * $sales_item->sales_num;
        }
        // dd($sales_items);
        $sales_detail->total_sales_num = $total_sales_num;
        $sales_detail->total_price = $beforeDis_total_price - $total_dis_price;
        $sales_detail->total_dis_price = $total_dis_price;
        $sales_detail->change_price = $init_deposit_price - $sales_detail->total_price;
        $sales_detail->save();
        foreach ($sales_items as $key => $sales_item) {
          $sale = new Sale;
          $sale->sales_detail_id = $sales_detail->id;
          $sale->barcode = $sales_item->barcode;
          $sale->num = $sales_item->sales_num;
          $sale->item_name = $sales_item->name;
          $sale->item_price = $sales_item->price;
          $sale->saled_at = $sales_detail->saled_at;
          $sale->save();
        }
        $base_sales_at->addHour();
      }
      $base_sales_at->subHours($howmuch_hour_cnt);
      $base_sales_at->addDay();
    }
    $base_sales_at->subDays($howmuch_date_num);
  }

  public function ajaxAccount(Request $request) {
    //販売明細と販売の作成。
    $sales_barcodes = $request->sales_barcodes;
    $barcodeSalesNumList = [];
    foreach ($sales_barcodes as $key => $sales_barcode) {
      if (array_key_exists($sales_barcode, $barcodeSalesNumList) !== true) {
        $barcodeSalesNumList[$sales_barcode] = 0;
      }
      $barcodeSalesNumList[$sales_barcode] += 1;
    }
    $sales_items = [];
    foreach ($barcodeSalesNumList as $barcode => $sales_num) {
      $sales_item = Item::find($barcode);
      $sales_item->sales_num = $sales_num;
      $sales_items[] = $sales_item;
    }

    $now_fmt = Carbon::now()->format('Y-m-d H:i:s');

    $branch_office_id = 1;
    $staff_id = 1;
    $init_deposit_price = 1000; //todo

    $sales_detail = new SalesDetail;
    $sales_detail->branch_office_id = $branch_office_id;
    $sales_detail->staff_id = $staff_id;
    $sales_detail->deposit_price = $init_deposit_price;
    $sales_detail->saled_at = $now_fmt;

    //todo関数化？
    $total_sales_num = 0;
    $beforeDis_total_price = 0;
    $total_dis_price = 0;
    foreach ($sales_items as $key => $sales_item) {
      //todoここも事前に送ってきてもらえそう
      $total_sales_num += $sales_item->sales_num;
      $beforeDis_total_price += $sales_item->price * $sales_item->sales_num;
      $total_dis_price += $sales_item->discount_amt * $sales_item->sales_num;
    }
    $sales_detail->total_sales_num = $total_sales_num;
    $sales_detail->total_price = $beforeDis_total_price - $total_dis_price;
    $sales_detail->total_dis_price = $total_dis_price;
    $sales_detail->change_price = $init_deposit_price - $sales_detail->total_price;
    $sales_detail->save();

    foreach ($sales_items as $key => $sales_item) {
      $sale = new Sale;
      $sale->sales_detail_id = $sales_detail->id;
      $sale->barcode = $sales_item->barcode;
      $sale->num = $sales_item->sales_num;
      $sale->item_name = $sales_item->name;
      $sale->item_price = $sales_item->price;
      $sale->saled_at = $sales_detail->saled_at;
      $sale->save();
    }

    return response()->json([
      'result' => 'success',
    ]);
  }

  public function toSalesHistoryReference(Request $request) {
    $sales_details = SalesDetail::select(DB::raw("sd.id, DATE_FORMAT(sd.saled_at, '%m月%d日 %H:%i') saled_at_fmt, sd.total_price, st.staff_number"))
      ->from('sales_details as sd')
      ->join('staffs as st', 'sd.staff_id', '=', 'st.id')
      ->get();
    return view('register.sales_history_reference', [
			'sales_details' => $sales_details,
    ]);
  }

  public function toSalesDetail(Request $request) {
    $sales_detail_id = $request->sales_detail_id;
    $sales_detail = SalesDetail::select(DB::raw('sd.total_price, sd.total_dis_price, (sd.total_price + sd.total_dis_price) as before_dis_total_price, sd.deposit_price, sd.change_price, sd.saled_at, st.staff_number'))
      ->from('sales_details as sd')
      ->join('staffs as st', 'sd.staff_id', '=', 'st.id')
      ->where('sd.id', $sales_detail_id)
      ->first();

    $sales = Sale::select(DB::raw('s.item_name, s.num, s.item_price, i.discount_amt, s.num * s.item_price as sub_total_price, s.num * i.discount_amt as sub_total_dis_price'))
      ->from('sales as s')
      ->join('items as i', function ($join) use ($sales_detail_id) {
        $join->on('s.barcode', '=', 'i.barcode')
          ->where('s.sales_detail_id', '=', $sales_detail_id);
      })
      ->get();

    // dd($sales);
    $sales_at = Carbon::parse($sales_detail->saled_at);
    $saled_at_fmt = $sales_at->format('Y年m月d日 H:i');
    return view('register.sales_detail', [
      'sales_detail' => $sales_detail,
      'sales' => $sales,
      'saled_at_fmt' => $saled_at_fmt,
    ]);
  }
}
