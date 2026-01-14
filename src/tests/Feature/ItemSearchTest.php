<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Item;
use App\Models\Like;

class ItemSearchTest extends TestCase
{
  use RefreshDatabase;
  // 1.「商品名」で部分一致検索ができる
  public function test_can_search_items_by_partial_match_of_name(): void{
    // ログインユーザー
    $loginUser = User::factory()->verified()->create();

    $item1 = Item::factory()->create(['name' => 'OTHER_ITEM']);
    $item2 = Item::factory()->create(['name' => 'LIKED_ITEM']);
    $item3 = Item::factory()->create(['name' => 'HIT_ITEM1']);
    $item4 = Item::factory()->create(['name' => 'HIT_ITEM2']);

    Like::create(['user_id' => $loginUser->id, 'item_id' => $item2->id]);
    Like::create(['user_id' => $loginUser->id, 'item_id' => $item3->id]);
    Like::create(['user_id' => $loginUser->id, 'item_id' => $item4->id]);

    // ログイン状態で一覧を開く
    $response = $this->actingAs($loginUser)->get('/');

    // 検索ボタン押下
    $response = $this->actingAs($loginUser)
      ->get('/?tab=mylist&keyword=' . urlencode('HIT'));

    $response->assertStatus(200);

    // 1
    $response->assertSee($item3->name);
    $response->assertSee($item4->name);

    $response->assertDontSee($item1->name);
    $response->assertDontSee($item2->name);
  }

  // 2.検索状態がマイリストでも保持されている
  public function test_search_keyword_is_kept_on_mylist_when_navigating_from_home(): void{
    $loginUser = User::factory()->verified()->create();

    $item1 = Item::factory()->create(['name' => 'OTHER_ITEM']);
    $item2 = Item::factory()->create(['name' => 'LIKED_ITEM']);
    $item3 = Item::factory()->create(['name' => 'HIT_ITEM1']);
    $item4 = Item::factory()->create(['name' => 'HIT_ITEM2']);

    Like::create(['user_id' => $loginUser->id, 'item_id' => $item2->id]);
    Like::create(['user_id' => $loginUser->id, 'item_id' => $item3->id]);
    Like::create(['user_id' => $loginUser->id, 'item_id' => $item4->id]);

    $response = $this->actingAs($loginUser)->get('/?keyword=' . urlencode('HIT'));
    $response->assertStatus(200);

    $mylist = $this->actingAs($loginUser)->get('/?tab=mylist&keyword=' . urlencode('HIT'));
    $mylist->assertStatus(200);

    // 2
    $mylist->assertSee('HIT_ITEM1');
    $mylist->assertSee('HIT_ITEM2');
    $mylist->assertDontSee('LIKED_ITEM');
  }
}
