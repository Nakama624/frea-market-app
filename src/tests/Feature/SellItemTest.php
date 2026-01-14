<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\Item;
use App\Models\Condition;
use App\Models\Category;

class SellItemTest extends TestCase
{
  use RefreshDatabase;
  // 商品出品画面にて必要な情報が保存できること
  // （カテゴリ、商品の状態、商品名、ブランド名、商品の説明、販売価格）
  public function test_add_sell_item(){
    // 画像フォルダを仮作成
    Storage::fake('public');

    $loginUser = User::factory()->verified()->create();

    $condition = Condition::factory()->create();

    // カテゴリー
    $category1 = Category::create(['category_name' => 'ファッション']);
    $category2 = Category::create(['category_name' => '家電']);
    $category3 = Category::create(['category_name' => 'インテリア']);

    $this->actingAs($loginUser)->get('/sell')->assertStatus(200);
    // 出品商品が保存される
    $sellItem = $this->actingAs($loginUser)
      ->post('/sell/', [
        'name' => '商品A',
        'price' => 1000,
        'brand' => 'ブランドA',
        'condition_id' => $condition->id,
        'description' => '説明A',
        'item_img' => UploadedFile::fake()->create('test.png', 100, 'image/png'),
        'category_ids' => [$category1->id, $category2->id],
      ])
      ->assertSessionHasNoErrors()
      ->assertStatus(302);

    // データが1件だけ作成される
    $this->assertDatabaseCount('items', 1);

    // $item->idを取得
    $item = Item::query()->latest('id')->first();
    $this->assertNotNull($item);

    // DB：商品情報
    $this->assertDatabaseHas('items', [
      'id' => $item->id,
      'name' => '商品A',
      'price' => 1000,
      'brand' => 'ブランドA',
      'condition_id' => $condition->id,
      'description' => '説明A',
      'sell_user_id' => $loginUser->id,
    ]);

    // DB：中間テーブル（categories_items）に紐付いている
    $this->assertDatabaseHas('categories_items', [
      'item_id' => $item->id,
      'category_id' => $category1->id,
    ]);
    $this->assertDatabaseHas('categories_items', [
      'item_id' => $item->id,
      'category_id' => $category2->id,
    ]);
    // カテゴリ3の設定はなし
    $this->assertDatabaseMissing('categories_items', [
      'item_id' => $item->id,
      'category_id' => $category3->id,
    ]);

    // 商品詳細ページ
    $response = $this->actingAs($loginUser)->get('/item/' . $item->id);
    $response->assertStatus(200);

    // 表示確認（商品情報）
    $response->assertSee($item->name);
    $response->assertSee($item->brand);
    $response->assertSee(number_format($item->price));
    $response->assertSee($item->description);
    $response->assertSee($condition->condition_name);
    $response->assertSee('storage/items/' . $item->item_img, false);

    // 表示確認（カテゴリ）
    $response->assertSee($category1->category_name);
    $response->assertSee($category2->category_name);
    $response->assertDontSee($category3->category_name);
  }
}
