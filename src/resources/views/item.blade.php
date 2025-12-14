@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/item.css') }}">
@endsection

@section('content')
<div class="content">
  <!-- 商品画像(左) -->
  <div class="left">
    <img class="item-img" src="{{ $item->img }}" alt="商品画像" />
  </div>

  <!-- 商品詳細(右) -->
  <div class="right">
    <h1 class="item-name">{{ $item->name }}</h1>
    <p class="item-brand">{{ $item->brand }}</p>
    <p class="item-price">¥<span class="item-price__num">{{ $item->price }}</span>（税込）</p>

    <!-- アイコン -->
    <div class="icon-group">
      <div class="icon__likes">
        <img class="icon" src="{{ asset('images/likes_default.png') }}" alt="いいねデフォルト" />
        <!-- <img class="icon" src="{{ asset('images/likes_pink.png') }}" alt="いいね" /> -->
        <p class="icon__count">12</p>
      </div>
      <div class="icon__comment">
        <img class="icon" src="{{ asset('images/comment.png') }}" alt="コメント" />
        <p class="icon__count">10</p>
      </div>
    </div>

    <div class="form__button">
      <a class="form__button-submit" href="/purchase/{{ $item->id }}">購入手続きへ</a>
    </div>

    <div class="sub-content">
      <h2 class="item-describe">商品説明</h2>
      <p class="item-description">{{ $item->description }}</p>
    </div>

    <div class="sub-content">
      <h2 class="item-info">商品の情報</h2>
      <div class="item-info__group">
        <p class="item-info__title">カテゴリー</p>
        <p class="item-categories">要修正</p>
      </div>
      <div class="item-info__group">
        <p class="item-info__title">商品の状態</p>
        <p class="item-condition">{{ $item->condition_id }}</p>
      </div>
    </div>

    <div class="sub-content">
      <h3 class="item-comment">コメント(数)</h3>

      <!-- プロフィール画像 -->
      <!-- コメントの数を繰り返す -->
      <div class="profile-box">
        <div class="profile-thumb">
          <img id="profilePreview" src="" alt="" />
        </div>
        <!-- ユーザー名 -->
        <span class="profile-name">
          ユーザー名を表示
        </span>
      </div>
      <!-- コメント表示 -->
      <p class="comments"></p>
      <!-- ここまで繰り返す     -->

      <!-- コメント入力 -->
      <form action="" class="form-comment">
        <p class="form-comment__title">商品へのコメント</p>
        <textarea class="form-comment__textarea" name="comment"></textarea>
        <!-- ボタン -->
        <div class="form__button">
          <button class="form__button-submit" type="submit">コメントを送信する</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection