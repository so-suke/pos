<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SalesTableSeeder extends Seeder {
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run() {
    DB::statement('SET FOREIGN_KEY_CHECKS=0');
    DB::table('sales')->truncate();
    DB::statement('SET FOREIGN_KEY_CHECKS=1');

    //本日から一週間後を最後とし、日付作成分、データを作成する
    $four_week = 7 * 4;
    $surplus_day = 0;
    // $surplus_day = 20;
    $make_day_num = $four_week + $surplus_day;
    $now = Carbon::now();
    $date = Carbon::parse($now->format('Y-m-d'))->subDays($make_day_num - 1); //subDaysの引数をマイナス1しないと今日が含まれない。

    //全てのおにぎりの商品barcodeを取得
    $items = DB::table('items')
      ->select('barcode')
      ->where('small_category_id', '=', 1)
      ->limit(3)
      ->get();

    $hour_max = 3;
    foreach ($items as $key => $item) {
      for ($day_i = 0; $day_i < $make_day_num; $day_i++) {
        for ($hour_i = 0; $hour_i < $hour_max; $hour_i++) {
          DB::table('sales')->insert([
            'barcode' => $item->barcode,
            'num' => rand(1, 3),
            'saled_at' => $date->format('Y-m-d H:i:s'),
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
          ]);
          $date->addHour();
        }
        $date->subHours($hour_max);
        $date->addDay();
      }
      $date->subDays($make_day_num);
    }
  }
}
