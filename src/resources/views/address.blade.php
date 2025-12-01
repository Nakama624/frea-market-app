@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/address.css') }}">
@endsection

@section('content')
<div class="content">
  <form action="/register" class="login-form">
    <h1 class="content-title">住所の変更</h1>
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
      <button class="form__button-submit" type="submit">変更する</button>
    </div>
  </form>
</div>
@endsection