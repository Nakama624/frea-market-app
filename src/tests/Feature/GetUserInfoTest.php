<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Item;
use App\Models\Payment;
use App\Models\Purchase;

class GetUserInfoTest extends TestCase
{
  use RefreshDatabase;

  // 1.必要な情報が取得できる
  // （プロフィール画像、ユーザー名、出品した商品一覧、購入した商品一覧）
  public function test_display_user_info(){
    $loginUser = User::factory()
      ->profileCompleted()
      ->verified()
      ->create();

    $otherUser = User::factory()
      ->profileCompleted()
      ->verified()
      ->create();

    // 購入商品
    $item1 = Item::factory()->soldBy($loginUser)->create(['name' => 'SELL']);
    $item2 = Item::factory()->soldBy($otherUser)->create(['name' => 'ITEM']);
    $item3 = Item::factory()->soldBy($otherUser)->create(['name' => 'BUY']);

    // 購入情報を作成
    $payment = Payment::factory()->create();
    
    $soldItem = Purchase::factory()
      ->create([
        'item_id' => $item3->id,
        'user_id' => $loginUser->id,
    ]);

    // 出品タブ
    $sellRes = $this->actingAs($loginUser)->get('/mypage?page=sell');
    $sellRes->assertStatus(200);
    $sellRes->assertSee($loginUser->name);
    $sellRes->assertSee('storage/profiles/' . $loginUser->profile_img, false);
    $sellRes->assertSee($item1->name); //soldBy($loginUser)

    $sellRes->assertDontSee($item2->name); //soldBy($otherUser)
    $sellRes->assertDontSee($item3->name); //soldBy($otherUser)

    // 購入タブ
    $buyRes = $this->actingAs($loginUser)->get('/mypage?page=buy');
    $buyRes->assertStatus(200);
    $buyRes->assertSee($loginUser->name);
    $buyRes->assertSee('storage/profiles/' . $loginUser->profile_img, false);
    $buyRes->assertSee($item3->name);

    $buyRes->assertDontSee($item1->name); //soldBy($loginUser)
    $buyRes->assertDontSee($item2->name);
  }
}
