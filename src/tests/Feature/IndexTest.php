<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Purchase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class IndexTest extends TestCase
{
  use RefreshDatabase;

  // 1.全商品を取得できる
  public function test_index_displays_all_items(){
    $loginUser = User::factory()->verified()->create();
    $otherUser = User::factory()->verified()->create();

    $sellItem = Item::factory()
      ->soldBy($loginUser)
      ->create(['name' => '出品商品']);

    $otherItem = Item::factory()
      ->soldBy($otherUser)
      ->create(['name' => '商品']);

    $response = $this->get('/');
    $response->assertStatus(200);

    // 1
    $response->assertSee($sellItem->name);
    $response->assertSee($otherItem->name);
  }

  // 2.購入済み商品は「Sold」と表示される
  public function test_sold_item_displays_sold_label(){
    $item1 = Item::factory()->create(['name' => 'ITEM1']);
    $item2 = Item::factory()->create(['name' => 'ITEM2']);
    $item3 = Item::factory()->create(['name' => 'SOLD_ITEM']);

    $soldItem = Purchase::factory()
      ->create([
        'item_id' => $item3->id,
    ]);

    $response = $this->get('/');
    $response->assertStatus(200);

    $response->assertSee($item1->name);
    $response->assertSee($item2->name);
    // 2
    // SOLD_ITEMの後ろにSoldがついているか確認
    $response->assertSeeInOrder([
      '<p class="item-group__name">SOLD_ITEM</p>',
      '<p class="item-group__sold">Sold</p>',
    ], false);
    // Sold表記が1つのみか確認
    $this->assertSame(
      1,
      substr_count($response->getContent(), '<p class="item-group__sold">Sold</p>')
    );
  }

  // 3.自分が出品した商品は表示されない
  public function test_index_no_display_my_sellitem(){
    $loginUser = User::factory()->verified()->create();
    $otherUser = User::factory()->verified()->create();

    $sellItem = Item::factory()
      ->soldBy($loginUser)
      ->create(['name' => '出品商品']);

    $otherItem = Item::factory()
      ->soldBy($otherUser)
      ->create(['name' => '商品']);

    $response = $this->actingAs($loginUser)->get('/');
    $response->assertStatus(200);

    // 3
    $response->assertDontSee($sellItem->name);

    $response->assertSee($otherItem->name);
  }
}
