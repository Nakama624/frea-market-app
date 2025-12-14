<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Payment;
use App\Models\Purchase;
use Illuminate\Support\Facades\Auth;

class ItemController extends Controller
{
  public function index(){
    $items = Item::all();
    // 購入済み商品のチェック
    $soldItemIds = Purchase::pluck('item_id')->toArray();

    return view('index', compact('items','soldItemIds'));
  }

  public function detail($item_id){
    $item = Item::find($item_id);
    $user = Auth::user();

    return view('item', compact('item','user'));
  }


  // 出品
  public function sell(){
    return view('sell');
  }

}
