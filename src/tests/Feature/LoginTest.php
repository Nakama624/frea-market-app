<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;

class LoginTest extends TestCase
{
  use RefreshDatabase;

  // 1.メールアドレスが入力されていない場合、バリデーションメッセージが表示される
  public function test_email_is_required_on_login(){
    $response = $this->post('/login', [
      'email' => '',
      'password' => 'password123',
    ]);

    $response->assertSessionHasErrors(['email']);
    $this->assertEquals('メールアドレスを入力してください', session('errors')->first('email'));
  }

  // 2.パスワードが入力されていない場合、バリデーションメッセージが表示される
  public function test_password_is_required_on_login(){
    $response = $this->post('/login', [
      'email' => 'test@example.com',
      'password' => '',
    ]);

    $response->assertSessionHasErrors(['password']);
    $this->assertEquals('パスワードを入力してください', session('errors')->first('password'));
  }

  // 3.入力情報が間違っている場合、バリデーションメッセージが表示される
  public function test_login_fails_with_unregistered_credentials_shows_message(){
    $response = $this->from('/login')->post('/login', [
      'email' => 'notfound@example.com',
      'password' => 'wrongpassword',
    ]);

    $this->assertGuest();
    $response->assertSessionHasErrors(['email']);
    $this->assertEquals('ログイン情報が登録されていません', session('errors')->first('email'));
    $response->assertRedirect('/login');
  }

  // 4.正しい情報が入力された場合、ログイン処理が実行される
  public function test_login_succeeds_with_correct_credentials(){
    $user = User::factory()->verified()->create([
      'email' => 'test@example.com',
    ]);

    $response = $this->post('/login', [
      'email' => 'test@example.com',
      'password' => 'password',
    ]);

    $this->assertAuthenticatedAs($user);
    $response->assertRedirect('/mypage');
  }
}