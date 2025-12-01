@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/mypage.css') }}">
@endsection

@section('content')
<div class="content">
  <form action="/register" class="login-form">
    <h1 class="content-title">プロフィール設定</h1>
    <!-- プロフィール画像 -->
    <div class="form-input">
      
    <!-- ★★ -->


    </div>
    <!-- ユーザー名 -->
    <div class="form-input">
      <span class="form__label--item">ユーザー名</span>
      <input type="name" name="name" class="form__input--item"
    value="{{ old('name') }}" />
    </div>
    <!-- 郵便番号 -->
    <div class="form-input">
      <span class="form__label--item">郵便番号</span>
      <input type="text" name="postcode" class="form__input--item"
    value="{{ old('postcode') }}" />
    </div>
    <!-- 住所 -->
    <div class="form-input">
      <span class="form__label--item">パスワード</span>
      <input type="text" name="address" class="form__input--item"/>
    </div>
    <!-- 建物 -->
    <div class="form-input">
      <span class="form__label--item">建物</span>
      <input type="text" name="building" class="form__input--item"/>
    </div>
    <!-- ボタン -->
    <div class="form__button">
      <button class="form__button-submit" type="submit">更新する</button>
    </div>
  </form>
</div>
@endsection