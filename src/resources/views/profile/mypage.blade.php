<!-- プロフィール画面 -->
@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/profile/mypage.css') }}">
@endsection

@section('content')
@if (session('success') && $tab === 'buy')
<div class="alert-success">
    {{ session('success') }}
</div>
@endif

@if (session('success') && $tab === 'sell')
<div class="alert-success">
    {{ session('success') }}
</div>
@endif

<div class="profile">
    <img src="{{ asset('storage/images/' . $user->image) }}" alt="ユーザーのプロフィール画像" class="user_icon">
    <div class="user-form">
        <p class="user_name">{{ $user->name }}</p>
        <!-- 評価表示 -->
        @if ($averageRating > 0)
        <div class="rating">
            @for ($i = 1; $i <= 5; $i++)
                <span class="star {{ $i <= round($averageRating) ? 'filled' : '' }}">★</span>
                @endfor
                <span class="rating-text"></span>
        </div>
        @else
        <p>評価なし</p>
        @endif
    </div>
    <a class="edit_link" href="{{ route('profile.edit') }}">
        <button class="profile_edit_button">プロフィールを編集</button>
    </a>
</div>

<div class="item__tab-buttons">
    <a href="{{ url('/mypage?tab=sell') }}" class="item__button-submit-second {{ $tab === 'sell' ? 'active' : '' }}">出品した商品</a>
    <a href="{{ url('/mypage?tab=buy') }}" class="item__button-submit-second {{ $tab === 'buy' ? 'active' : ''}}">購入した商品</a>
    <a href="{{ url('/mypage?tab=transaction') }}" class="item__button-submit-second {{ $tab === 'transaction' ? 'active' : ''}}">取引中の商品
        @if ($transactionTabUnread > 0)
        <div class="tab-notification-badge-form">
            <span class="tab-notification-badge">{{ $transactionTabUnread }}</span>
        </div>
        @endif
    </a>
</div>

{{-- 出品・購入・取引中の商品タブのときだけ商品を表示 --}}
@if (in_array($tab, ['buy', 'sell', 'transaction']) && isset($items))
<div class="item-row">
    @foreach($items as $data)
    {{-- リンク先の決定：transactionタブなら取引チャット画面へ、それ以外なら商品詳細へ --}}
    @php
    $item = ($tab === 'transaction') ? $data->item: $data;
    // transactionタブの場合、取引詳細ページへのリンクを作成
    $transaction = ($tab === 'transaction') ? $data :null;
    $linkRoute = ($tab === 'transaction') ? 'transaction.show' : 'items.show';
    $linkParam = ($tab === 'transaction') ? ['transaction' => $transaction->id] : ['item' => $item->id];
    @endphp

    {{-- リンクを作成 --}}
    <a href="{{ route($linkRoute, $linkParam) }}" class="item-card-link">
        <div class="item-card">
            <div class="item-image-container">
                <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->title }}" class="item-image">

                {{-- 未読バッジは取引中タグの時だけ表示 --}}
                @if ($tab === 'transaction')
                    @php
                        $unread = $transaction ? $transaction->unreadCountForUser(auth()->id()) : 0; // nullチェックを追加
                    @endphp

                    @if ($unread > 0)
                        <span class="notification-badge">{{ $unread }}</span>
                    @endif
                @endif
            </div>

            <h5 class="title-header">
                <span class="item-title">{{ $item->title }}</span>
            </h5>
        </div>
    </a>
    @endforeach
</div>
@endif
@endsection