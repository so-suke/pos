<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemsTable extends Migration {
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up() {
    Schema::create('items', function (Blueprint $table) {
      $table->increments('barcode');
      $table->unsignedInteger('small_category_id');
      $table->string('name');
      $table->integer('price');
      $table->integer('discount_amt');
      $table->integer('stock');
      $table->string('img_name');
      $table->timestamps();

      $table->foreign('small_category_id')->references('id')->on('small_categories');

    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down() {
    Schema::dropIfExists('items');
  }
}
