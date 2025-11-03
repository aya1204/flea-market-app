<!-- 商品一覧画面のbladeファイル -->
@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/item/mylist.css') }}">
@endsection

@section('content')
@if (session('success'))
<div class="alert-success">
    {{ session('success') }}
</div>
@endif

@php
$tab = request()->query('tab', 'recommend');
$keyword = request('item_name') ?? session('search_keyword');
@endphp

<div class="coachtech__content-second">
    <div class="coachtech__tab-buttons">
        <a href="{{ route('items.search', ['tab' => 'recommend', 'item_name' => $keyword]) }}" class="coachtech__button-submit-second {{ $tab === 'recommend' ? 'active' : ''}}">おすすめ</a>
        <a href="{{ route('items.index', ['tab' => 'mylist', 'item_name' => $keyword]) }}" class="coachtech__button-submit-second {{ $tab === 'mylist' ? 'active' : ''}}">マイリスト</a>
        @if($tab === 'search')
        <span class="coachtech__button-submit-second active">検索結果</span>
        @endif
    </div>

    <!-- 商品一覧 -->
    <div class="item">
        <div class="item-row">
            @if ($tab === 'mylist' && $show_message)
            <p class="show_message">マイリストを表示するにはログインしてください。</p>
            @endif
            <div class="right_column">
                @foreach($items as $item)
                <a href="{{ route('items.show', $item->id) }}" class="item-card-link">
                    <div class="item-card">
                        <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->title }}" class="item-image">
                        {{-- 購入済みならSold表示 --}}
                        @if ($item->is_sold)
                        <span class="sold_label">Sold</span>
                        @endif
                        <h5 class="title-header">
                            <span class="item-title">{{ $item->title }}</span>
                        </h5>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
    </div>
</div>
</div>
@endsection