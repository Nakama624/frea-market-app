<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Item;
use App\Models\Purchase;
use App\Models\Payment;

class PurchaseTest extends TestCase
{
  use RefreshDatabase;

  // 1.「購入する」ボタンを押下すると購入が完了する
  // 2.購入した商品は商品一覧画面にて「sold」と表示される
  // 3.「プロフィール/購入した商品一覧」に追加されている
  public function test_logged_in_user_buy_item_and_display_sold_item(){
    $loginUser = User::factory()->verified()->create();
    $otherUser = User::factory()->verified()->create();

    // $seller = User::factory()->verified()->create();
    $item1 = Item::factory()->soldBy($otherUser)->create(['name' => 'ITEM']);
    $item2 = Item::factory()->soldBy($otherUser)->create(['name' => 'BOUGHT']);

    // ログイン状態で商品詳細を開く
    $response = $this->actingAs($loginUser)->get('/purchase/' . $item2->id);
    $response->assertStatus(200);

    $payment = Payment::factory()->create();

    // 購入する
    $bought = $this->actingAs($loginUser)
      ->post('/purchase/' . $item2->id, [
        'delivery_postcode' => '111-2222',
        'delivery_address'  => '東京都八王子市111',
        'delivery_building' => 'テストビル101',
        'payment_id'        => $payment->id,
      ])
      ->assertSessionHasNoErrors()
      ->assertStatus(302);

    // データが1件だけ作成される
    $this->assertDatabaseCount('purchases', 1);

    // Purchaseの確認
    $this->assertDatabaseHas('purchases', [
      'user_id'           => $loginUser->id,
      'item_id'           => $item2->id,
      'delivery_postcode' => '111-2222',
      'delivery_address'  => '東京都八王子市111',
      'payment_id'        => $payment->id,
      'status'            => 'pending',
    ]);

    // 2.購入した商品は一覧で「Sold」と表示される
    $response = $this->get('/');
    $response->assertStatus(200);

    $response->assertSee($item1->name);
    $response->assertSee($item2->name);

    $response->assertSeeInOrder([
      '<p class="item-group__name">BOUGHT</p>',
      '<p class="item-group__sold">Sold</p>',
    ], false);
    // Sold表記が1つのみか確認
    $this->assertSame(
      1,
      substr_count($response->getContent(), '<p class="item-group__sold">Sold</p>')
    );

    // 3.購入商品はマイリストの「購入した商品」タブで表示される
    $response = $this->actingAs($loginUser)->get('/mypage?page=buy');
    $response->assertStatus(200);

    $response->assertSee($item2->name);
    
    // BOUGHTの後ろにSoldがついているか確認
    $response->assertSeeInOrder([
      '<p class="item-group__name">BOUGHT</p>',
      '<p class="item-group__sold">Sold</p>',
    ], false);
    // Sold表記が1つのみか確認
    $this->assertSame(
      1,
      substr_count($response->getContent(), '<p class="item-group__sold">Sold</p>')
    );

    $response->assertDontSee($item1->name);
  }
}
