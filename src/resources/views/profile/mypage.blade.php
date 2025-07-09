<!-- プロフィール画面 -->
@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/profile/mypage.css') }}">
@endsection

<!-- 購入が完了しましたメッセージ -->
@section('content')
@if (session('success') && $tab === 'buy')
<div class="alert-success">
    {{ session('success')}}
</div>
@endif

<!-- 出品が完了しましたメッセージ -->
@if (session('success') && $tab === 'sell')
    <div class="alert-success">
        {{ session('success')}}
    </div>
@endif

<div class="profile">
    <img src="{{ asset('storage/images/' . $user->image) }}" alt="ユーザーのプロフィール画像" class="user_icon">
    <p class="user_name">{{ $user->name }}</p>
    <a class="edit_link" href="{{ route('profile.edit') }}">
        <button class="profile_edit_button">プロフィールを編集</button>
    </a>
</div>

<div class="item__tab-buttons">
    <a href="{{ url('/mypage?tab=sell') }}" class="item__button-submit-second {{ $tab === 'sell' ? 'active' : '' }}">出品した商品</a>
    <a href="{{ url('/mypage?tab=buy') }}" class="item__button-submit-second {{ $tab === 'buy' ? 'active' : ''}}">購入した商品</a>
</div>

{{-- 出品・購入タブのときだけ商品を表示 --}}
@if (in_array($tab, ['buy', 'sell']) && isset($items))
    <div class="item-row">
        @forelse($items as $item)
            <a href="{{ route('items.show', $item->id) }}" class="item-card-link">
                <div class="item-card">
                    <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->title }}" class="item-image">
                    <h5 class="title-header">
                        <span class="item-title">{{ $item->title }}</span>
                    </h5>
                </div>
            </a>
            @empty
            <p>商品がありません。</p>
        @endforelse
    </div>
@endif
@endsection