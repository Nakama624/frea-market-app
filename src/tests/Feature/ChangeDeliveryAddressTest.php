<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Item;
use App\Models\Purchase;
use App\Models\Payment;

class ChangeDeliveryAddressTest extends TestCase
{
  use RefreshDatabase;

  // 1.送付先住所変更画面にて登録した住所が商品購入画面に反映されている
  // 2.購入した商品に送付先住所が紐づいて登録される
  public function test_change_delivery_address(){
    // ログインユーザー（購入者）
    $loginUser = User::factory()->verified()->create();
    $otherUser = User::factory()->verified()->create();

    $item = Item::factory()->soldBy($otherUser)->create(['name' => 'PURCHASE_ITEM']);

    $payment = Payment::create([
      'payment_method' => 'カード払い',
    ]);

    // 住所変更画面で住所が保存される
    $res = $this->actingAs($loginUser)
      ->post('/purchase/address/' . $item->id, [
        'delivery_postcode' => '111-2222',
        'delivery_address'  => '配送先：東京都八王子市111',
        'delivery_building' => '配送先：テストビル101',
      ])
      ->assertSessionHasNoErrors();

    $purchasePage = $this->actingAs($loginUser)
      ->get('/purchase/' . $item->id);

    // 1.変更された住所が住所変更画面で表示されている
    $purchasePage->assertStatus(200);
    $purchasePage->assertSee('〒111-2222');
    $purchasePage->assertSee('配送先：東京都八王子市111');
    $purchasePage->assertSee('配送先：テストビル101');

    // 2.購入画面で（Purchase）で変更された住所で保存される
    $buy = $this->actingAs($loginUser)
      ->post('/purchase/' . $item->id, [
        'payment_id' => $payment->id,
        'delivery_postcode' => '111-2222',
        'delivery_address'  => '配送先：東京都八王子市111',
        'delivery_building' => '配送先：テストビル101',])
      ->assertSessionHasNoErrors();

    // データが1件だけ作成される
    $this->assertDatabaseCount('purchases', 1);

    // DBに正しく保存されていることを確認
    $this->assertDatabaseHas('purchases', [
      'user_id'           => $loginUser->id,
      'item_id'           => $item->id,
      'delivery_postcode' => '111-2222',
      'delivery_address'  => '配送先：東京都八王子市111',
      'delivery_building' => '配送先：テストビル101',
      'payment_id'        => $payment->id,
      'status'            => 'pending',
    ]);
  }
}
