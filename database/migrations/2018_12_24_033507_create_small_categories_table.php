<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSmallCategoriesTable extends Migration {
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up() {
    Schema::create('small_categories', function (Blueprint $table) {
      $table->increments('id');
      $table->unsignedInteger('large_category_id');
      $table->string('name');
      $table->timestamps();

      $table->foreign('large_category_id')->references('id')->on('large_categories');

    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down() {
    Schema::dropIfExists('small_categories');
  }
}
