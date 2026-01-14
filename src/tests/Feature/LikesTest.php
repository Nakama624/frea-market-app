<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Item;
use App\Models\Like;

class LikesTest extends TestCase
{
  use RefreshDatabase;

  // 1.いいねアイコンを押下することによって、いいねした商品として登録することができる。
  // 2.追加済みのアイコンは色が変化する
  public function test_increase_likes_item_and_icon_has_active_color(){
    $loginUser = User::factory()->verified()->create();

    $likeItem = Item::factory()->create(['name' => 'LIKED_ITEM']);

    // 事前のいいねは0件
    $this->assertDatabaseMissing('likes', [
      'item_id' => $likeItem->id,
      'user_id' => $loginUser->id,
    ]);

    // いいねを押す前を確認
    $noLike = $this->get('/item/' . $likeItem->id);
    $noLike->assertStatus(200);

    $noLike->assertSee('<p class="icon__count">0</p>', false);

    $noLike->assertDontSee('likes_pink.png', false);
    $noLike->assertSee('likes_default.png', false);

    // 1.いいね押下
    $res = $this->actingAs($loginUser)
      ->from('/item/' . $likeItem->id)
      ->post('/items/' . $likeItem->id . '/like')
      ->assertSessionHasNoErrors()
      ->assertStatus(302);

    // データが1件だけ作成される
    $this->assertDatabaseCount('likes', 1);

    $res->assertRedirect('/item/' . $likeItem->id);

    // DBに正しく保存されていることを確認
    $this->assertDatabaseHas('likes', [
      'item_id' => $likeItem->id,
      'user_id' => $loginUser->id,
    ]);

    // ログイン状態で商品詳細を開く
    $response = $this->actingAs($loginUser)->get('/item/' . $likeItem->id);
    $response->assertStatus(200);

    // いいねが1つ増えている
    $response->assertSee('<p class="icon__count">1</p>', false);

    // いいね押下するとアイコンの色が変わっている(画像)
    $response->assertSee('likes_pink.png', false);
    $response->assertDontSee('likes_default.png', false);
  }

  // 3.再度いいねアイコンを押下することによって、いいねを解除することができる。
  public function test_decrease_likes_item(){
    $loginUser = User::factory()->verified()->create();

    $unLikeItem = Item::factory()->create(['name' => 'UNLIKED_ITEM']);

    // 事前いいねを作る
    Like::create([
      'item_id' => $unLikeItem->id,
      'user_id' => $loginUser->id,
    ]);

    // DBのいいねを確認
    $this->assertDatabaseHas('likes', [
        'item_id' => $unLikeItem->id,
        'user_id' => $loginUser->id,
    ]);

    // いいね押下
    $res = $this->actingAs($loginUser)
      ->from('/item/' . $unLikeItem->id)
      ->post('/items/' . $unLikeItem->id . '/like')
      ->assertSessionHasNoErrors();

    $res->assertRedirect('/item/' . $unLikeItem->id);

    // いいね解除確認
    $this->assertDatabaseMissing('likes', [
      'item_id' => $unLikeItem->id,
      'user_id' => $loginUser->id,
    ]);

    // ログイン状態で商品詳細を開く
    $response = $this->actingAs($loginUser)->get('/item/' . $unLikeItem->id);
    $response->assertStatus(200);

    // いいねが減っている
    $response->assertSee('<p class="icon__count">0</p>', false);

    // いいね解除するとアイコンの色が戻っている(画像)
    $response->assertDontSee('likes_pink.png', false);
    $response->assertSee('likes_default.png', false);
  }
}
