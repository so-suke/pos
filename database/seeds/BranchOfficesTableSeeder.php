<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BranchOfficesTableSeeder extends Seeder {
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run() {
    DB::statement('SET FOREIGN_KEY_CHECKS=0');
    DB::table('branch_offices')->truncate();
    DB::statement('SET FOREIGN_KEY_CHECKS=1');

		$now_fmt = Carbon::now()->format('Y-m-d H:i:s');

    for ($i = 0; $i < 3; $i++) {
      DB::table('branch_offices')->insert([
        'name' => 'branch_office_' . $i,
        'phone_number' => '0120221234',
        'street_address' => 'aaa県aaa市aaa町a丁目',
        'created_at' => $now_fmt,
        'updated_at' => $now_fmt,
      ]);
    }
  }
}
