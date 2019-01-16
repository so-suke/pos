<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStaffsTable extends Migration {
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up() {
    Schema::create('staffs', function (Blueprint $table) {
      $table->increments('id');
      $table->unsignedInteger('branch_office_id');
      $table->string('staff_number');
      $table->string('name');
      $table->timestamps();

      $table->foreign('branch_office_id')->references('id')->on('branch_offices');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down() {
    Schema::dropIfExists('staffs');
  }
}
