<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StaffsTableSeeder extends Seeder {
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run() {
    DB::statement('SET FOREIGN_KEY_CHECKS=0');
    DB::table('staffs')->truncate();
    DB::statement('SET FOREIGN_KEY_CHECKS=1');

    $now_fmt = Carbon::now()->format('Y-m-d H:i:s');

    for ($branch_office_id = 1; $branch_office_id <= 3; $branch_office_id++) {
      $base_staff_number = $branch_office_id * 100;
      for ($i = 0; $i < 3; $i++) {
        DB::table('staffs')->insert([
          'branch_office_id' => $branch_office_id,
          'staff_number' => $base_staff_number + $i,
          'name' => 'name_' . $i,
          'created_at' => $now_fmt,
          'updated_at' => $now_fmt,
        ]);
      }
    }
  }
}
