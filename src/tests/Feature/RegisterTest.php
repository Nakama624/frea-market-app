<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RegisterTest extends TestCase
{
  use RefreshDatabase;

  private function validRegisterData(array $overrides = []): array {
    return array_merge([
      'name' => 'テスト',
      'email' => 'test@example.com',
      'password' => 'password123',              // 8文字以上
      'password_confirmation' => 'password123', // 一致
    ], $overrides);
  }

  private function assertFirstErrorMessage(string $field, string $expected): void{
    $this->assertEquals($expected, session('errors')->first($field));
  }

  // 1.名前が入力されていない場合、バリデーションメッセージが表示される
  public function test_register_name_required_message_is_shown(){
    $response = $this->post('/register', $this->validRegisterData([
      'name' => '',
    ]));

    $response->assertSessionHasErrors(['name']);
    $this->assertFirstErrorMessage('name', 'お名前を入力してください');
  }

  // 2.メールアドレスが入力されていない場合、バリデーションメッセージが表示される
  public function test_register_email_required_message_is_shown(){
    $response = $this->post('/register', $this->validRegisterData([
      'email' => '',
    ]));

    $response->assertSessionHasErrors(['email']);
    $this->assertFirstErrorMessage('email', 'メールアドレスを入力してください');
  }

  // 3.パスワードが入力されていない場合、バリデーションメッセージが表示される
  public function test_register_password_required_message_is_shown(){
    $response = $this->post('/register', $this->validRegisterData([
      'password' => '',
      'password_confirmation' => '',
    ]));

    $response->assertSessionHasErrors(['password']);
    $this->assertFirstErrorMessage('password', 'パスワードを入力してください');
  }

  // 4.パスワードが7文字以下の場合、バリデーションメッセージが表示される
  public function test_register_password_min_8_message_is_shown(){
    $response = $this->post('/register', $this->validRegisterData([
      'password' => '1234567',
      'password_confirmation' => '1234567',
    ]));

    $response->assertSessionHasErrors(['password']);
    $this->assertFirstErrorMessage('password', 'パスワードは8文字以上で入力してください');
  }

  // 5.パスワードが確認用パスワードと一致しない場合、バリデーションメッセージが表示される
  public function test_register_password_confirmation_mismatch_message_is_shown(){
    $response = $this->post('/register', $this->validRegisterData([
      'password' => 'password123',
      'password_confirmation' => 'password999',
    ]));

    $response->assertSessionHasErrors(['password']);
    $this->assertFirstErrorMessage('password', 'パスワードと一致しません');
  }

  // 6.全ての項目が入力されている場合、会員情報が登録され、プロフィール設定画面に遷移される
  public function test_register_success_creates_user_and_redirects_to_profile(){
    $data = $this->validRegisterData([
      'email' => 'ok@example.com',
    ]);

    $response = $this->post('/register', $data);

    // 会員情報が登録される
    $this->assertDatabaseHas('users', [
      'name' => $data['name'],
      'email' => $data['email'],
    ]);

    // メール認証誘導画面に遷移
    $response->assertRedirect('/auth/mail');
  }
}