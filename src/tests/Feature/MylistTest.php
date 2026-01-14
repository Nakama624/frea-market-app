<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Item;
use App\Models\Like;
use App\Models\User;
use App\Models\Purchase;

// 1.いいねした商品だけが表示される
class MylistTest extends TestCase
{
  use RefreshDatabase;

  public function test_only_likes_items_on_mylist(){
    $loginUser = User::factory()->verified()->create();
    $otherUser = User::factory()->verified()->create();

    $item1 = Item::factory()->soldBy($otherUser)->create(['name' => 'OTHER_ITEM']);
    $item2 = Item::factory()->soldBy($otherUser)->create(['name' => 'LIKED_ITEM']);

    Like::create(['user_id' => $loginUser->id, 'item_id' => $item2->id]);

    $response = $this->actingAs($loginUser)->get('/?tab=mylist');
    $response->assertStatus(200);

    // 1
    $response->assertSee($item2->name);
    $response->assertDontSee($item1->name);
  }

  // 2.購入済み商品は「Sold」と表示される
  public function test_sold_item_displays_sold_label(){
    $loginUser = User::factory()->verified()->create();
    $otherUser = User::factory()->verified()->create();

    $item1 = Item::factory()->soldBy($otherUser)->create(['name' => 'OTHER_ITEM']);
    $item2 = Item::factory()->soldBy($otherUser)->create(['name' => 'LIKED_ITEM']);
    $item3 = Item::factory()->soldBy($otherUser)->create(['name' => 'SOLD_ITEM']);

    Like::create(['user_id' => $loginUser->id, 'item_id' => $item2->id]);
    Like::create(['user_id' => $loginUser->id, 'item_id' => $item3->id]);

    $soldItem = Purchase::factory()
      ->create([
        'user_id' => $loginUser->id,
        'item_id' => $item3->id,
    ]);

    $response = $this->actingAs($loginUser)->get('/?tab=mylist');
    $response->assertStatus(200);

    $response->assertSee($item2->name);
    
    // 2.SOLD_ITEMの後ろにSoldがついているか確認
    $response->assertSeeInOrder([
      '<p class="item-group__name">SOLD_ITEM</p>',
      '<p class="item-group__sold">Sold</p>',
    ], false);
    // Sold表記が1つのみか確認
    $this->assertSame(
      1,
      substr_count($response->getContent(), '<p class="item-group__sold">Sold</p>')
    );

    $response->assertDontSee($item1->name);
  }

  // 3.未認証の場合は何も表示されない
  public function test_mylist_is_empty_when_not_authenticated(){
    $loginUser = User::factory()->verified()->create();
    $otherUser = User::factory()->verified()->create();

    $item1 = Item::factory()->soldBy($otherUser)->create(['name' => 'OTHER_ITEM']);
    $item2 = Item::factory()->soldBy($otherUser)->create(['name' => 'LIKED_ITEM']);

    Like::create(['user_id' => $loginUser->id, 'item_id' => $item2->id]);

    $response = $this->get('/?tab=mylist');
    $response->assertStatus(200);

    // 3
    $response->assertDontSee($item2->name);
    $response->assertDontSee($item1->name);
  }
}
