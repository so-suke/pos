<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SmallCategoriesTableSeeder extends Seeder {

  public function run() {
    DB::statement('SET FOREIGN_KEY_CHECKS=0');
    DB::table('small_categories')->truncate();
    DB::statement('SET FOREIGN_KEY_CHECKS=1');

    $small_categories = [[1, 'onigiri'], [1, 'bento'], [2, 'hotsnack'], [2, 'chukaman']];
    // $small_categories = [[1, 'おにぎり'], [1, '弁当'], [2, '揚げ物'], [2, '中華まん']];
    foreach ($small_categories as $key => $small_category) {
      DB::table('small_categories')->insert([
        'large_category_id' => $small_category[0],
        'name' => $small_category[1],
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
      ]);
    }
  }
}
