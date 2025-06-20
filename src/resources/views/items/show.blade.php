<!-- 商品詳細画面 -->
@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/item/show.css') }}">
@endsection

@section('content')
<div class="item_detail">
    <div class="col-md-3">
        <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->title }}" class="item_image">
    </div>
    <div class="col-md-9">
        <div class="item-row">
            <h1 class="item_title">{{ $item->title}}</h1>
            <p class="item_brand_name">{{ $item->brand->name ?? 'ブランド未設定' }}</p>
            <p class="item_price"> ¥{{ number_format($item->price)}} <span class="tax">(税込)</span></p>
            <div class="icon_group">
                {{-- お気に入りアイコン表示 --}}
                @if (auth()->check())
                {{-- ログイン済み --}}
                @if (auth()->user()->favorites && auth()->user()->favorites->contains($item->id))
                {{-- 登録済み（赤スター） --}}
                <form action="{{ route('favorites.destroy', $item->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="favorite_button">
                        <img src="{{ asset('storage/images/favorite_icon.png') }}" alt="お気に入り登録済み" class="favorited_icon">
                    </button>
                </form>
                @else
                {{-- 未登録（グレースター） --}}
                <form action="{{ route('favorites.store', $item->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="favorite_button">
                        <img src="{{ asset('storage/images/favorite_icon.png') }}" alt="お気に入り未登録" class="unfavorite_icon">
                    </button>
                </form>
                @endif
                @else
                {{-- 未ログイン（ログインページへ誘導） --}}
                <a href="{{ route('login') }}" class="favorite_button">
                    <img src="{{ asset('storage/images/favorite_icon.png') }}" alt="ログインしてお気に入り登録" class="unfavorite_icon">
                </a>
                @endif
                {{-- コメントアイコン（共通表示） --}}
                <a href="/item/:item_id/comment" class="comment_icon_button">
                    <img src="{{ asset('storage/images/comment_icon.png')}}" alt="コメントを書く" class="comment_icon">
                </a>
                {{-- お気に入り登録数とコメント --}}
                <div class="counts">
                    {{-- お気に入り登録数 --}}
                    <p class="favorite_count">{{ optional($item->favoritedByUsers)->count() ?? 0 }}</p>
                    {{-- コメント数 --}}
                    <p class="comment_count"> {{ optional($item->comments)->count() ?? 0 }}</p>
                </div>
            </div>

            {{-- 購入ボタン表示 --}}
            @if (!$item->is_sold)
            <a href="{{ route('purchase.index', ['item' => $item->id]) }}" class="purchase_button">購入手続きへ</a>
            @else
            <p class="sold_label">Sold</p>
            @endif

            <div class="item_description">
                <h3 class="description_title">商品説明</h3>
                <p class="description">{{ $item->description }}</p>
            </div>
            <h3 class="information">商品の情報</h3>
            <div class="category">
                <h5 class="category_title">カテゴリー</h5>
                @if ($item->categories->isNotEmpty())
                <ul class="category_list">
                    @foreach ($item->categories as $category)
                    <li class="category_name">{{ $category->name }}</li>
                    @endforeach
                </ul>
                @else
                <p>カテゴリー未設定</p>
                @endif
            </div>
            <div class="conditions">
                <h5 class="item_condition">商品の状態</h5>
                <p class="condition">{{ $item->condition->name ?? '状態未設定' }}</p>
            </div>

            <div class="item_comments">
                <h3 class="comment">コメント({{ $item->comments->count() }})</h3>

                {{-- コメント一覧 --}}
                @foreach ($item->comments as $comment)
                <div class="comment_user_profile">
                    <div class="comment_wrapper">
                        {{-- プロフィール画像 --}}
                        @if ($comment->user && $comment->user->image)
                        <img src="{{ asset('storage/images/' . $comment->user->image) }}" alt="ユーザー画像" class="user_icon">
                        @else
                        <img src="{{ asset('storage/images/default_user_icon.png') }}" alt="デフォルトアイコン" class="user_icon">
                        @endif

                        {{-- ユーザー名 --}}
                        <h5 class="user_name">{{ $comment->user->name ?? '退会ユーザー' }}</h5>
                    </div>
                    {{-- コメント本文 --}}
                    <p class="user_comment">{{ $comment->comment }}</p>
                    @endforeach

                    <h5 class="comment_title">商品へのコメント</h5>
                    @if ($errors->has('comment'))
                    <p class="validation_error">
                        {{ $errors->first('comment') }}
                    </p>
                    @endif

                    <form action="{{ route('item.comment', $item->id) }}" method="POST">
                        @csrf
                        <textarea name="comment" id="" class="comment_text"></textarea>
                        <button class="send">コメントを送信する</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endsection