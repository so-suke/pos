<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesTable extends Migration {
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up() {
    Schema::create('sales', function (Blueprint $table) {
      $table->increments('id');
      $table->unsignedInteger('sales_detail_id');
      $table->unsignedInteger('barcode');
      $table->integer('num');
      $table->string('item_name');
      $table->integer('item_price');
      $table->dateTime('saled_at');
      $table->timestamps();

      $table->foreign('sales_detail_id')->references('id')->on('sales_details');
      $table->foreign('barcode')->references('barcode')->on('items');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down() {
    Schema::dropIfExists('sales');
  }
}
