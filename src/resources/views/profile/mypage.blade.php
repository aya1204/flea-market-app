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
    <!-- 未読の取引メッセージがある場合通知マークを表示する -->
    <a href="{{ url('/mypage?tab=sell') }}" class="item__button-submit-second {{ $tab === 'sell' ? 'active' : '' }}">出品した商品</a>
    <a href="{{ url('/mypage?tab=buy') }}" class="item__button-submit-second {{ $tab === 'buy' ? 'active' : ''}}">購入した商品</a>
    <a href="{{ url('/mypage?tab=transaction') }}" class="item__button-submit-second {{ $tab === 'transaction' ? 'active' : ''}}">取引中の商品
        @if ($transactionTabUnread > 0)
        <span class="tab-notification-badge">{{ $transactionTabUnread}}</span>
        @endif
    </a>
</div>

{{-- 出品・購入・取引中の商品タブのときだけ商品を表示 --}}
@if (in_array($tab, ['buy', 'sell', 'transaction']) && isset($items))
<div class="item-row">
    @forelse($items as $item)
    {{-- @php
            $link = $tab === 'transaction'
                ? route('transaction.show', $item->transaction->id)
                : route('items.show', $item->id);
        @endphp --}}
    @php
    // 取引中タブの場合は transaction の id を渡す
    if ($tab === 'transaction') {
    $link = route('transaction.show', $item->transaction->id);
    } else {
    $link = route('items.show', $item->id);
    }
    @endphp

    <a href="{{ $link }}" class="item-card-link">
        <div class="item-card">
            <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->title }}" class="item-image">
            <h5 class="title-header">
                <span class="item-title">{{ $item->title }}</span>
            </h5>
            {{-- 未読バッジは取引中タグの時だけ表示 --}}
            @if ($tab === 'transaction')
            @php
            $transaction = $item->transaction; // Itemモデルに transaction() リレーションが必要
            $unread = $transaction->unreadCountForUser(auth()->id());
            @endphp
            @if ($unread > 0)
            <span class="notification-badge">{{ $unread }}</span>
            @endif
            @endif
        </div>
    </a>
    @empty
    <p>商品がありません。</p>
    @endforelse
</div>
@endif
@endsection