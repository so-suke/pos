<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesDetailsTable extends Migration {
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up() {
    Schema::create('sales_details', function (Blueprint $table) {
      $table->increments('id');
      $table->unsignedInteger('branch_office_id');
      $table->unsignedInteger('staff_id');
      $table->integer('total_price');
      $table->integer('total_dis_price');
      $table->integer('total_sales_num');
      $table->integer('deposit_price');
      $table->integer('change_price');
			$table->dateTime('saled_at');
      $table->timestamps();

      $table->foreign('branch_office_id')->references('id')->on('branch_offices');
      $table->foreign('staff_id')->references('id')->on('staffs');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down() {
    Schema::dropIfExists('sales_details');
  }
}
