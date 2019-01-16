<?php

namespace App\Http\Controllers;

use App\Item;
use App\LargeCategory;
use App\Sales;
use App\SmallCategory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Log;

class ManagementController extends Controller {
  public function toLargeCategories() {
    $large_categories = LargeCategory::get();

    return view('contents.large_categories', [
      'large_categories' => $large_categories,
    ]);
  }

  public function toSmallCategories(Request $request, $lc_id) {
    $request->session()->put('lc_id', $lc_id);
    return view('contents.small_categories', [
    ]);
  }

  public function getSmallCategoriesInitData(Request $request) {
    Log::debug(1);
    $large_category_id = $request->session()->get('lc_id');
    $small_categories = SmallCategory::where('large_category_id', $large_category_id)
      ->get();

    $now = Carbon::now();
    $start = $now->copy()->subDays(7);
    $end = $now->copy()->subDay();

    include app_path() . '/includes/variables.php';
    //本日の(一週間前)から(本日)までの日付と曜日の配列を作成。
    for ($i = 0, $clone_date = $start->copy(); $i < 8; $i++) {
      $dates[] = ['num' => $clone_date->day, 'day_of_week' => $day_of_week_name_list[$clone_date->dayOfWeek]];
      $clone_date->addDay();
    }

    $saleses = Sales::select(DB::raw("sc.id, sc.name, SUM(s.num) AS sum_num, FLOOR(SUM(s.item_price) / 100) AS sum_money, DATE_FORMAT(s.saled_at, '%Y-%m-%d') AS time"))
      ->from('sales AS s')
      ->join('items as i', function ($join) use ($start, $end, $now, $large_category_id) {
        $join->on('s.barcode', '=', 'i.barcode')
          ->whereRaw("(
							DATE_FORMAT(s.saled_at, '%Y-%m-%d') BETWEEN ? AND ? OR
							DATE_FORMAT(s.saled_at, '%Y-%m-%d') = ?
						)", [$start->format('Y-m-d'), $end->format('Y-m-d'), $now->format('Y-m-d')])
          ->whereRaw('i.small_category_id in (
							select id
							from small_categories
							where large_category_id = ?
						)', [$large_category_id]);
      })
      ->join('small_categories as sc', 'i.small_category_id', '=', 'sc.id')
      ->groupBy(DB::raw("DATE_FORMAT(s.saled_at, '%Y-%m-%d')"), 'sc.id', 'sc.name')
      ->get();

    //小カテゴリidをキーとして、販売情報(販売数、販売小計)を値とする。
    $sc_id_saleses = [];
    foreach ($saleses as $key => $sales) {
      if (isset($sc_id_saleses[$sales->id]) === false) {
        $sc_id_saleses[$sales->id] = [
          'sc_name' => $sales->name,
        ];
      }
      $sc_id_saleses[$sales->id]['list'][] = [
        'num' => $sales->sum_num,
        'money' => $sales->sum_money,
      ];
    }

    return response()->json([
      'small_categories' => $small_categories,
      'dates' => $dates,
      'sc_id_saleses' => $sc_id_saleses,
    ]);
  }

  public function toSmallCategory(Request $request, $sc_id) {
    $request->session()->put('sc_id', $sc_id);

    return view('contents.small_category');
  }

//日付別販売情報: 各商品の販売日時(年月日)、合計販売数、小計金額を取得。
  public function _getDailySaleses($sc_id, $start, $end, $now) {
    $saleses = Sales::select(DB::raw("items.name, DATE_FORMAT(saled_at, '%Y-%m-%d') AS time, SUM(num) AS sum_num, SUM(num) * items.price AS sum_money"))
      ->join(
        DB::raw("
            (SELECT *
            FROM items
            WHERE small_category_id='$sc_id') AS items
        "), 'sales.barcode', '=', 'items.barcode'
      )
      ->whereBetween(DB::raw("DATE_FORMAT(saled_at, '%Y-%m-%d')"), [$start->format('Y-m-d'), $end->format('Y-m-d')])
      ->orWhere(DB::raw("DATE_FORMAT(saled_at, '%Y-%m-%d')"), $now->format('Y-m-d'))
      ->groupBy(DB::raw("DATE_FORMAT(saled_at, '%Y-%m-%d')"), 'items.name', 'items.price')
      ->get();

    return $saleses;
  }

  public function get_small_category_init_data(Request $request) {
    $sc_id = $request->session()->get('sc_id');
    $now = Carbon::now();
    $start = $now->copy()->subDays(7);
    $end = $now->copy()->subDay();

    include app_path() . '/includes/variables.php';
    //日付と曜日の配列を作成。
    for ($i = 0, $clone_date = $start->copy(); $i < 8; $i++) {
      $dates[] = ['num' => $clone_date->day, 'day_of_week' => $day_of_week_name_list[$clone_date->dayOfWeek]];
      $clone_date->addDay();
    }

    $start_fmt_at_all = $now->copy()->subDays(7 * 4)->format('Y/m/d') . '(月)';
    $end_fmt_at_all = $now->format('Y/m/d') . '(月)';

    $saleses = $this->_getDailySaleses($sc_id, $start, $end, $now);
    $item_name_saleses = [];
    $item_name_saleses = $this->_getPrepareAllItemSalesInfo_inSpecifyDays($sc_id, $item_name_saleses, $start, $now);
    $item_name_saleses = $this->_getItemNameAsKey_SalesValue($saleses, $item_name_saleses);

    $date_keies = $this->_getDayKeiesForItemNameSaleses($start, $now);
    $item_name_saleses = $this->_getTotalSalesForDisplayDate($item_name_saleses, $date_keies);
    $item_name_saleses = $this->_getCalcedCompRatio($item_name_saleses);

    //contents.small_category.blade.php へ行きます。
    return response()->json([
      'small_category' => SmallCategory::find($sc_id),
      'start_fmt_at_all' => $start_fmt_at_all,
      'end_fmt_at_all' => $end_fmt_at_all,
      'dates' => $dates,
      'item_name_saleses' => $item_name_saleses,
    ]);
  }

  public function tz_prev_day(Request $request) {
    $now_fmt_in_tz = $request->get('now_fmt_in_tz');
    $now = Carbon::parse(substr($now_fmt_in_tz, 0, -5))->subDay();

    return $this->get_small_category_timezone_data_core($now, $request);
  }

  public function tz_next_day(Request $request) {
    $now_fmt_in_tz = $request->get('now_fmt_in_tz');
    $now = Carbon::parse(substr($now_fmt_in_tz, 0, -5))->addDay();

    return $this->get_small_category_timezone_data_core($now, $request);
  }

  public function get_small_category_timezone_data_core($now, $request) {
    $sc_id = $request->session()->get('sc_id');
    //時間帯別販売数を取得
    $saleses = Sales::select(DB::raw("items.name, HOUR(saled_at) as hour, SUM(num) as sum_num"))
      ->join(
        DB::raw("
            (SELECT *
            FROM items
            WHERE small_category_id='$sc_id') AS items
        "), 'sales.barcode', '=', 'items.barcode'
      )
      ->where(DB::raw("DATE_FORMAT(saled_at, '%Y-%m-%d')"), $now->format('Y-m-d'))
      ->groupBy(DB::raw("HOUR(saled_at), items.name"))
      ->get();

    //商品名をキーとした、各時間帯販売数とその合計販売数を設定。
    $item_name_sales_nums = [];
    $item_name_sum_sales_nums = [];
    foreach ($saleses as $key => $sales) {
      if (isset($item_name_sales_nums[$sales->name]) === false) {
        $item_name_sales_nums[$sales->name] = array_fill(0, 24, 0);
        $item_name_sum_sales_nums[$sales->name] = 0;
      }
      $item_name_sales_nums[$sales->name][$sales->hour] = $sales->sum_num;
      $item_name_sum_sales_nums[$sales->name] += $sales->sum_num;
    }

    //表示用、現在日付を設定。
    include app_path() . '/includes/variables.php';
    $now_fmt = $now->format('Y/m/d') . '(' . $day_of_week_name_list[$now->dayOfWeek] . ')';

    return response()->json([
      'now_fmt' => $now_fmt,
      'item_name_sales_nums' => $item_name_sales_nums,
      'item_name_sum_sales_nums' => $item_name_sum_sales_nums,
    ]);
  }

  public function get_small_category_timezone_data(Request $request) {
    $now = Carbon::now();
    return $this->get_small_category_timezone_data_core($now, $request);
  }

  //事前に全商品の販売情報を指定日数分作ってしまう。本日分は最後になる。
  public function _getPrepareAllItemSalesInfo_inSpecifyDays($sc_id, $item_name_saleses, $start, $now) {
    $items = Item::where('small_category_id', $sc_id)->get();
    foreach ($items as $key => $item) {
      $item_name_saleses[$item->name] = [];
      for ($day_cnt = 0, $start_copy = $start->copy(); $day_cnt < 7; $day_cnt++) {
        $item_name_saleses[$item->name][$start_copy->format('Y-m-d')] = [
          'num' => 0,
          'money' => 0,
        ];
        $start_copy->addDay();
      }
      $item_name_saleses[$item->name][$now->format('Y-m-d')] = [
        'num' => 0,
        'money' => 0,
      ];
    }

    return $item_name_saleses;
  }

  //商品名をキーとして、販売情報(販売数、販売小計)を値とする。
  public function _getItemNameAsKey_SalesValue($saleses, $item_name_saleses) {
    foreach ($saleses as $key => $sales) {
      $item_name_saleses[$sales->name][$sales->time] = [
        'num' => $sales->sum_num,
        'money' => $sales->sum_money,
      ];
    }
    return $item_name_saleses;
  }

  //item_name_salesesのための日付キー配列。
  public function _getDayKeiesForItemNameSaleses($start, $now) {
    $date_keies = [];
    for ($day_cnt = 0, $start_copy = $start->copy(); $day_cnt < 7; $day_cnt++) {
      $date_keies[] = $start_copy->format('Y-m-d');
      $start_copy->addDay();
    }
    $date_keies[] = $now->format('Y-m-d');

    return $date_keies;
  }

  //日付毎(本日から8日前から本日まで)の合計販売数と合計販売金額を設定。
  public function _getTotalSalesForDisplayDate($item_name_saleses, $date_keies) {
    for ($day_idx = 0; $day_idx < 8; $day_idx++) {
      $key = $date_keies[$day_idx];
      $item_name_saleses['合計'][$key] = ['num' => 0, 'money' => 0];
      foreach ($item_name_saleses as $item_name => $saleses) {
        $item_name_saleses['合計'][$key]['num'] += $saleses[$key]['num'];
        $item_name_saleses['合計'][$key]['money'] += $saleses[$key]['money'];
      }
    }
    return $item_name_saleses;
  }

  //構成比を計算する。
  public function _getCalcedCompRatio($item_name_saleses) {
    include app_path() . '/includes/functions.php';
    foreach ($item_name_saleses as $name => $saleses) {
      foreach ($saleses as $idx => $sales) {
        $item_name_saleses[$name][$idx]['composition_ratio'] = num2per($sales['money'], $item_name_saleses['合計'][$idx]['money'], 1);
      }
    }
    return $item_name_saleses;
  }

  public function prevWeek(Request $request, $week_move_num) {
    $now = Carbon::now();
    $sc_id = $request->session()->get('sc_id');
    //表示上の始まりの日、終わりの日、取得。
    $start = $now->copy()->subDays(7 * $week_move_num);
    $end = $start->copy()->addDays(6);

    //表示する一週間分の日付を設定。//todo: get_small_category_init_dataと処理が重複しているので、関数にまとめる
    include app_path() . '/includes/variables.php';
    for ($i = 0, $clone_date = $start->copy(); $i < 7; $i++) {
      $dates[] = ['num' => $clone_date->day, 'day_of_week' => $day_of_week_name_list[$clone_date->dayOfWeek]];
      $clone_date->addDay();
    }
    $dates[] = ['num' => $now->day, 'day_of_week' => $day_of_week_name_list[$now->dayOfWeek]];

    $saleses = $this->_getDailySaleses($sc_id, $start, $end, $now);
    $item_name_saleses = [];
    $item_name_saleses = $this->_getPrepareAllItemSalesInfo_inSpecifyDays($sc_id, $item_name_saleses, $start, $now);
    $item_name_saleses = $this->_getItemNameAsKey_SalesValue($saleses, $item_name_saleses);

    $date_keies = $this->_getDayKeiesForItemNameSaleses($start, $now);
    $item_name_saleses = $this->_getTotalSalesForDisplayDate($item_name_saleses, $date_keies);
    $item_name_saleses = $this->_getCalcedCompRatio($item_name_saleses);

    return response()->json([
      'dates' => $dates,
      'item_name_saleses' => $item_name_saleses,
    ]);
  }
}