@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/register.css') }}">
@endsection

@section('content')
<div class="content">
  <form action="/register" class="login-form">
    <h1 class="content-title">会員登録</h1>
    <!-- ユーザー名 -->
    <div class="form-input">
      <span class="form__label--item">ユーザー名</span>
      <input type="name" name="name" class="form__input--item"
    value="{{ old('name') }}" />
    </div>
    <!-- メールアドレス -->
    <div class="form-input">
      <span class="form__label--item">メールアドレス</span>
      <input type="email" name="email" class="form__input--item"
    value="{{ old('email') }}" />
    </div>
    <!-- パスワード -->
    <div class="form-input">
      <span class="form__label--item">パスワード</span>
      <input type="password" name="password" class="form__input--item"/>
    </div>
    <!-- 確認用パスワード -->
    <div class="form-input">
      <span class="form__label--item">確認用パスワード</span>
      <input type="password" name="password-conf" class="form__input--item"/>
    </div>
    <!-- ボタン -->
    <div class="form__button">
      <button class="form__button-submit" type="submit">登録する</button>
      <a class="form__button-tran" href="/login">ログインはこちら</a>
    </div>
  </form>
</div>
@endsection