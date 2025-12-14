<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
  use HasFactory;

  protected $fillable = [
    'name',
    'price',
    'brand',
    'description',
    'img',
    'condition_id',
    'sell_user_id',
  ];
  public function condition()
  {
    return $this->belongsTo(condition::class);
  }

  // リレーション
  public function user(){
    return $this -> belongsTo('App\Models\User');
  }
}
