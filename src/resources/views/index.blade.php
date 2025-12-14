@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection

@section('content')
<div class="content">
  <div class="form-change">
    <a class="form-change__recommend {{ request('tab') !== 'mylist' ? 'active' : '' }}" href="/">
    おすすめ</a>
    @if (Auth::check())
      <a class="form-change__mylist {{ request('tab') === 'mylist' ? 'active' : '' }}" href="/?tab=mylist">
      マイリスト</a>
    @endif
  </div>
  <div class="item-group">
    @foreach ($items as $item)
    <div class="item-group__row">
      <!-- 商品画像 -->
      <a href="/item/{{ $item->id }}">
        <img class="item-group__img" src="{{ $item->img }}" alt="商品画像" />
      </a>
      <!-- 商品名 -->
      <div class="item-group__name-sold">
        <p class="item-group__name">{{ $item->name }}</p>
        @if(in_array($item->id, $soldItemIds))
          <p class="item-group__sold">SOLD</p>
        @endif
      </div>
    </div>
    @endforeach
  </div>
</div>
@endsection