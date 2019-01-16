<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBranchOfficesTable extends Migration {
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up() {
    Schema::create('branch_offices', function (Blueprint $table) {
      $table->increments('id');
      $table->string('name');
      $table->string('phone_number');
      $table->string('street_address');
      $table->timestamps();
			// id, name, phone_number, street_address
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down() {
    Schema::dropIfExists('branch_offices');
  }
}
