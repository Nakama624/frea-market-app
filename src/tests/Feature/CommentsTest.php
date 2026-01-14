<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Item;
use App\Models\Condition;

class CommentsTest extends TestCase
{

  use RefreshDatabase;

  // 1.ログイン済みのユーザーはコメントを送信できる
  public function test_logged_in_user_can_post_comment_and_comment_count_increases(){
    $loginUser = User::factory()->verified()->create();

    $commentItem = Item::factory()->create(['name' => 'ITEM1']);

    // 事前は0件（増加テストのため）
    $this->assertDatabaseMissing('comments', [
      'item_id' => $commentItem->id,
      'user_id' => $loginUser->id,
    ]);

    // ログインしてコメントを送信
    $res = $this->actingAs($loginUser)
      ->post('/item/' . $commentItem->id, [
          'comment' => 'コメント追加テスト',
      ]);

    // エラーが出ていないことを確認
    $res->assertSessionHasNoErrors();
    $res->assertStatus(302);

    // データが1件だけ作成される
    $this->assertDatabaseCount('comments', 1);

    $res->assertRedirect('/item/' . $commentItem->id);

    // DBに登録されていることを確認
    $this->assertDatabaseHas('comments', [
      'item_id' => $commentItem->id,
      'user_id' => $loginUser->id,
      'comment' => 'コメント追加テスト',
    ]);

    // ログイン状態で商品詳細を開く
    $response = $this->actingAs($loginUser)->get('/item/' . $commentItem->id);
    $response->assertStatus(200);

    // コメント数
    $response->assertSee('コメント(1)');
  }

  // 2.ログイン前のユーザーはコメントを送信できない
  public function test_unlogged_in_user_cannot_post_comment(){
    $loginUser = User::factory()->verified()->create();

    $commentItem = Item::factory()->create(['name' => 'ITEM1']);

    // 事前は0件（増加テストのため）
    $this->assertDatabaseMissing('comments', [
      'item_id' => $commentItem->id,
      'user_id' => $loginUser->id,
    ]);

    // 2.ログインなしでコメントを送信
    $res = $this
      ->post('/item/' . $commentItem->id, [
          'comment' => 'コメント追加テスト',
      ]);

    // 未ログインならログイン画面へ
    $res->assertRedirect('/login');

    // DBに登録されていないことを確認
    $this->assertDatabaseMissing('comments', [
      'item_id' => $commentItem->id,
      'user_id' => $loginUser->id,
      'comment' => 'コメント追加テスト',
    ]);

    // 商品詳細を開く
    $response = $this->get('/item/' . $commentItem->id);
    $response->assertStatus(200);

    // コメント数増加なし
    $response->assertSee('コメント(0)');
  }

  // 3.コメントが入力されていない場合、バリデーションメッセージが表示される
  public function test_comment_required(){
    $loginUser = User::factory()->verified()->create();

    $condition = Condition::create([
      'condition_name' => '新品',
    ]);

    $commentItem = Item::factory()->create(['name' => 'ITEM1']);

    // ログインしてコメントを「空」で送信する
    $response = $this->actingAs($loginUser)
      ->from('/item/' . $commentItem->id)
      ->post('/item/' . $commentItem->id, [
        'comment' => '',
    ]);

    $response->assertRedirect('/item/' . $commentItem->id);
    // 4.バリデーションが表示される
    $response->assertSessionHasErrors(['comment']);

    // DBに保存されていない
    $this->assertDatabaseMissing('comments', [
      'item_id' => $commentItem->id,
      'user_id' => $loginUser->id,
      'comment' => '',
    ]);
  }

  // コメントが255字以上の場合、バリデーションメッセージが表示される
  // ※機能要件＆基本設計書では「最大文字数255文字」、
  // 　テストケースには「255文字以上は不可」と相違があるが、機能要件に寄せる
  // 　255文字→OK、256文字→不可
  public function test_comment_max_255(){
    $loginUser = User::factory()->verified()->create();

    $commentItem = Item::factory()->create(['name' => 'ITEM1']);

    // ＝＝256文字の場合エラー＝＝
    $comment256 = str_repeat('a', 256);
    // ログインしてコメントを送信
    $commentErr = $this->actingAs($loginUser)
      ->from('/item/' . $commentItem->id)
      ->post('/item/' . $commentItem->id, [
            'comment' => $comment256,
    ]);

    $commentErr->assertRedirect('/item/' . $commentItem->id);
    // 4.バリデーションを表示
    $commentErr->assertSessionHasErrors(['comment']);

    // DBに保存されていない
    $this->assertDatabaseMissing('comments', [
      'item_id' => $commentItem->id,
      'user_id' => $loginUser->id,
      'comment' => $comment256,
    ]);

    // ＝＝255文字の場合保存される＝＝
    $comment255 = str_repeat('a', 255);
    // ログインしてコメントを送信
    $response = $this->actingAs($loginUser)
      ->from('/item/' . $commentItem->id)
      ->post('/item/' . $commentItem->id, [
        'comment' => $comment255,
    ]);

    // エラーが出ていないことを確認
    $response->assertSessionHasNoErrors();
    $response->assertStatus(302);

    // DBに保存されている
    $this->assertDatabaseHas('comments', [
      'item_id' => $commentItem->id,
      'user_id' => $loginUser->id,
      'comment' => $comment255,
    ]);
  }
}
