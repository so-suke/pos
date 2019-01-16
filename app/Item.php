<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Item extends Model {
  // public $incrementing = false;
  protected $primaryKey = 'barcode';
}
