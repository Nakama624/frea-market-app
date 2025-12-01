@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endsection

@section('content')
<div class="content">
  <form action="/register" class="login-form">
    <h1 class="content-title">ログイン</h1>
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
    <div class="form__button">
      <button class="form__button-submit" type="submit">ログインする</button>
      <a class="form__button-tran" href="/register">会員登録はこちら</a>
    </div>
  </form>
</div>
@endsection