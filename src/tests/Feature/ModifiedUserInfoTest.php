<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;

class ModifiedUserInfoTest extends TestCase
{
  use RefreshDatabase;

  // 1.変更項目が初期値として過去設定されていること
  // （プロフィール画像、ユーザー名、郵便番号、住所）
  public function test_modify_user_info(){
    $loginUser = User::factory()
      ->profileCompleted()
      ->verified()
      ->create();

    $response = $this->actingAs($loginUser)->get('/mypage/profile');
    $response->assertStatus(200);

    // プロフィール情報の表示
    $response->assertSee($loginUser->name);
    $response->assertSee($loginUser->postcode);
    $response->assertSee($loginUser->address);
    $response->assertSee($loginUser->building);
    $response->assertSee('storage/profiles/' . $loginUser->profile_img, false);
  }
}
