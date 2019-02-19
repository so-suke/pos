<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ItemsTableSeeder extends Seeder {
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run() {
    DB::statement('SET FOREIGN_KEY_CHECKS=0');
    DB::table('items')->truncate();
    DB::statement('SET FOREIGN_KEY_CHECKS=1');

    for ($i = 0, $onigiri_prices = [110, 120, 130]; $i < 8; $i++) {
      $price = $onigiri_prices[array_rand($onigiri_prices)];
      $sale_discount = 100;
      $discount_amt = $price - $sale_discount;
      $items[] = ['small_category_id' => 1, 'name' => 'おにぎり_' . $i, 'price' => $price, 'discount_amt' => $discount_amt, 'img_name' => ''];
    }
    for ($i = 0; $i < 8; $i++) {
      $items[] = ['small_category_id' => 2, 'name' => '弁当_' . $i, 'price' => 100, 'discount_amt' => 50, 'img_name' => ''];
    }
		include(app_path().'/includes/items_tbl_seeder_simple_items.php');
    foreach ($hotsnacks as $key => $hotsnack) {
      $price = $hotsnack['price'];
      $discount_amt = (int) ($price * 0.1);
      $items[] = ['small_category_id' => 3, 'name' => $hotsnack['name'], 'price' => $price, 'discount_amt' => $discount_amt, 'img_name' => $hotsnack['img_name']];
    }
    foreach ($chukamans as $key => $chukaman) {
      $price = $chukaman['price'];
      $discount_amt = (int) ($price * 0.1);
      $items[] = ['small_category_id' => 4, 'name' => $chukaman['name'], 'price' => $price, 'discount_amt' => $discount_amt, 'img_name' => $chukaman['img_name']];
    }

    foreach ($items as $key => $item) {
      DB::table('items')->insert([
        'small_category_id' => $item['small_category_id'],
        'name' => $item['name'],
        'price' => $item['price'],
        'discount_amt' => $item['discount_amt'],
        'stock' => 10,
        'img_name' => $item['img_name'],
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
      ]);
    }
  }
}
