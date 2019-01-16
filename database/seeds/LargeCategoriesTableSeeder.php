<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LargeCategoriesTableSeeder extends Seeder {
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run() {
    DB::statement('SET FOREIGN_KEY_CHECKS=0');
    DB::table('large_categories')->truncate();
    DB::statement('SET FOREIGN_KEY_CHECKS=1');

    $large_category_names = ['米飯', 'FF'];
    foreach ($large_category_names as $key => $name) {
      DB::table('large_categories')->insert([
        'name' => $name,
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
      ]);
    }
  }
}
