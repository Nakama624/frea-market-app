<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Illuminate\Auth\Notifications\VerifyEmail;

class MailAuthTest extends TestCase
{
  use RefreshDatabase;

  // 1.会員登録後、認証メールが送信される
  public function test_register_sends_verification_email(): void{
    Notification::fake();

    $response = $this
      ->post('/register', [
        'name' => 'テストユーザー',
        'email' => 'test@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
      ]);

    $this->assertAuthenticated();

    $user = User::where('email', 'test@example.com')->firstOrFail();

    // 認証メール(VerifyEmail通知)が送られていること
    Notification::assertSentTo($user, VerifyEmail::class);

    $response->assertStatus(302);
  }

  // 2.認証誘導画面で「認証はこちらから」ボタンを押下できる
  public function test_verification_notice_has_link_button(): void{
    $user = User::factory()->unverified()->create();

    $response = $this->actingAs($user)->get('/email/verify');
    $response->assertStatus(200);

    $response->assertSee('認証はこちらから', false);
    $response->assertSee('href="/dev/mailhog/open"', false);
  }

  // 3.メール認証を完了すると、プロフィール設定画面に遷移する
  public function test_email_verification_redirects_to_profile_page(): void{
    $user = User::factory()
      ->unverified()
      ->create([
        'email' => 'verifyme@example.com',
      ]);

    $url = URL::temporarySignedRoute(
      'verification.verify',
      now()->addMinutes(60),[
        'id' => $user->id,
        'hash' => sha1($user->email),
      ]
    );

    $response = $this->actingAs($user)->get($url);

    // 認証が付いたことを確認
    $this->assertNotNull($user->fresh()->email_verified_at);

    $response->assertRedirect('/mypage/profile');
  }
}
