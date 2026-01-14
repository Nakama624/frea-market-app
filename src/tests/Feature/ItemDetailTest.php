<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Item;
use App\Models\Like;
use App\Models\Comment;
use App\Models\Category;

class ItemDetailTest extends TestCase
{
  use RefreshDatabase;
  // 1.必要な情報が表示される（商品画像、商品名、ブランド名、価格、いいね数、コメント数、商品説明、商品情報
  // （カテゴリ、商品の状態）、コメント数、コメントしたユーザー情報、コメント内容）
  // 2.複数選択されたカテゴリが表示されているか
  public function test_get_items_detail(){
    $loginUser = User::factory()->verified()->create();
    $commentUser = User::factory()->verified()->create();

    $item1 = Item::factory()->create(['name' => 'ITEM1']);
    $item2 = Item::factory()->create(['name' => 'ITEM2']);

    // いいね
    Like::create(['user_id' => $loginUser->id, 'item_id' => $item1->id]);
    Like::create(['user_id' => $commentUser->id, 'item_id' => $item1->id]);
    Like::create(['user_id' => $loginUser->id, 'item_id' => $item2->id]);

    // コメント
    Comment::create([
      'item_id' => $item1->id,
      'user_id' => $commentUser->id,
      'comment' => '別のユーザーのコメント（ITEM1）',
    ]);

    Comment::create([
      'item_id' => $item1->id,
      'user_id' => $loginUser->id,
      'comment' => 'ログインユーザーのコメント（ITEM1）',
    ]);

    Comment::create([
      'item_id' => $item2->id,
      'user_id' => $loginUser->id,
      'comment' => 'ログインユーザーのコメント（ITEM2）',
    ]);

    // カテゴリー
    $category1 = Category::create(['category_name' => 'ファッション']);
    $category2 = Category::create(['category_name' => '家電']);
    $category3 = Category::create(['category_name' => 'インテリア']);

    $item1->categories()->attach([$category1->id, $category2->id, $category3->id]);
    $item2->categories()->attach([$category1->id, $category2->id]);

    // ＝＝COMMENT_ITEM1の確認＝＝
    $response = $this->get('/item/' . $item1->id);
    $response->assertStatus(200);

    $response->assertSee($item1->name);
    $response->assertSee($item1->brand);
    $response->assertSee(number_format($item1->price));
    $response->assertSee($item1->description);
    $response->assertSee($item1->condition_name);
    $response->assertSee('storage/items/' . $item1->item_img, false);

    // コメント情報の確認
    $response->assertSee($commentUser->name);
    $response->assertSee('別のユーザーのコメント（ITEM1）');
    $response->assertSee($loginUser->name);
    $response->assertSee('ログインユーザーのコメント（ITEM1）');

    // カテゴリの確認
    $response->assertSee($category1->category_name);
    $response->assertSee($category2->category_name);
    $response->assertSee($category3->category_name);

    // いいね数
    $response->assertSee('<p class="icon__count">2</p>', false);
    // コメント数
    $response->assertSee('コメント(2)');

    // 1.＝＝COMMENT_ITEM2の確認＝＝
    $response = $this->get('/item/' . $item2->id);
    $response->assertStatus(200);

    $response->assertSee($item2->name);
    $response->assertSee($item2->brand);
    $response->assertSee(number_format($item2->price));
    $response->assertSee($item2->description);
    $response->assertSee($item2->condition_name);
    $response->assertSee('storage/items/' . $item2->item_img, false);

    // コメント情報の確認
    $response->assertSee($loginUser->name);
    $response->assertSee('ログインユーザーのコメント（ITEM2）');

    // 2.カテゴリの確認
    $response->assertSee($category1->category_name);
    $response->assertSee($category2->category_name);
    $response->assertDontSee($category3->category_name);

    // いいね数
    $response->assertSee('<p class="icon__count">1</p>', false);
    // コメント数
    $response->assertSee('コメント(1)');
  }
}
