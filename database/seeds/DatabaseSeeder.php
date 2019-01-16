<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder {
  /**
   * Seed the application's database.
   *
   * @return void
   */
  public function run() {
    $this->call([
      BranchOfficesTableSeeder::class,
      StaffsTableSeeder::class,
      LargeCategoriesTableSeeder::class,
      SmallCategoriesTableSeeder::class,
      ItemsTableSeeder::class,
      // SalesDetailsTableSeeder::class,
      // SalesTableSeeder::class,
    ]);
  }
}
