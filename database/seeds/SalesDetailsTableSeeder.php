<?php

use Illuminate\Database\Seeder;

class SalesDetailsTableSeeder extends Seeder {
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run() {
		//後で必要作ると思います。
    // DB::statement('SET FOREIGN_KEY_CHECKS=0');
    // DB::table('sales_details')->truncate();
    // DB::statement('SET FOREIGN_KEY_CHECKS=1');

    // $now_fmt = Carbon::now()->format('Y-m-d H:i:s');

    // for ($branch_office_id = 1; $branch_office_id <= 3; $branch_office_id++) {
    //   $base_staff_number = $branch_office_id * 100;
    //   for ($i = 0; $i < 3; $i++) {
    //     DB::table('sales_details')->insert([
    //       'branch_office_id' => $branch_office_id,
    //       'staff_id' => $staff_id,
    //       'total_price' => $total_price,
    //       'total_dis_price' => $total_dis_price,
    //       'total_sales_num' => $total_sales_num,
    //       'deposit_price' => $deposit_price,
    //       'change_price' => $change_price,
    //       'saled_at' => $saled_at,
    //       'created_at' => $now_fmt,
    //       'updated_at' => $now_fmt,
    //     ]);
    //   }
    // }
  }

}
