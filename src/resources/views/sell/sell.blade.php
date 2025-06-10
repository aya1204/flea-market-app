@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/sell/sell.css') }}">
@endsection

@section('content')
<div class="sell-container">
    <h1 class="title">商品の出品</h1>

    <form action="{{ route('sell') }}" method="POST" enctype="multipart/form-data" class="sell_form">
        @csrf

        <!-- 商品画像 -->
        <div class="image_form">
            <p class="image-title">商品画像</p>
            <div class="image-select_form">
                <label for="image" class="image-label">画像を選択する</label>
                <input type="file" id="image" name="image" class="hidden-file-input">
            </div>
        </div>

        <!-- 商品の詳細 -->
        <div class="item-detail">
            <h3 class="item-detail-title">商品の詳細</h3>
            <div class="category-group">
                <label class="category">カテゴリー</label>
                <div class="checkboxes">
                    @php
                    $categories = [
                    'ファッション', '家電', 'インテリア', 'レディース', 'メンズ', 'コスメ', '本', 'ゲーム', 'スポーツ', 'キッチン', 'ハンドメイド', 'アクセサリー', 'おもちゃ', 'ベビー・キッズ'
                    ];
                    @endphp
                    @foreach($categories as $category)
                    <label class="checkbox-label">
                        <input type="checkbox" name="categories[]" value="{{ $category }}">
                        <span class="category-check_button">
                            {{ $category }}
                        </span>
                    </label>
                    @endforeach
                </div>
            </div>
            <!-- セレクトボックス -->
            <!-- カスタムセレクトボックス -->
            <div class="select-group">
                <label for="condition" class="condition">商品の状態</label>
                <div class="custom-select-wrapper">
                    <div class="custom-select-trigger">商品の状態を選択してください
                        <span class="custom-select-trigger-triangle">▼</span>
                    </div>
                    <div class="custom-options">
                        <div class="custom-option" data-value="良好">良好</div>
                        <div class="custom-option" data-value="目立った傷や汚れなし">目立った傷や汚れなし</div>
                        <div class="custom-option" data-value="やや傷や汚れあり">やや傷や汚れあり</div>
                        <div class="custom-option" data-value="状態が悪い">状態が悪い</div>
                    </div>
                    <input type="hidden" name="condition" id="condition">
                </div>
            </div>

            <!-- <div class="select-group">
                    <label for="condition" class="condition">商品の状態</label>
                    <select name="condition" id="condition" class="condition-select">
                        <option value="" style="display: none">選択してください</option>
                        <option value="良好">良好</option>
                        <option value="目立った傷や汚れなし">目立った傷や汚れなし</option>
                        <option value="やや傷や汚れあり">やや傷や汚れあり</option>
                        <option value="状態が悪い">状態が悪い</option>
                    </select>
                </div> -->
        </div>

        <!-- 商品名と説明 -->
        <div class="item-group">
            <h3 class="item-group-title">商品名と説明</h3>
            <div class="input-group">
                <div class="name-group">
                    <label for="name" class="name">商品名</label>
                    <input type="text" id="name" name="name" class="text-box">
                </div>

                <div class="brand-group">
                    <label for="brand" class="brand">ブランド名</label>
                    <input type="text" id="brand" name="brand" class="text-box">
                </div>

                <div class="description-group">
                    <label for="description" class="description">商品の説明</label>
                    <textarea id="description" name="description" rows="6" class="textarea"></textarea>
                </div>

                <div class="price-group">
                    <label for="price" class="price">販売価格</label>
                    <div class="price-wrapper">
                        <input type="number" id="price" name="price" placeholder="¥" class="price-text-box">
                    </div>
                </div>
            </div>
        </div>

        <button type="submit" class="sell-submit">出品する</button>
    </form>
</div>

@endsection

@section('js')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const wrapper = document.querySelector('.custom-select-wrapper');
        const trigger = document.querySelector('.custom-select-trigger');
        const options = document.querySelector('.custom-options');
        const hiddenInput = document.querySelector('input[name="condition"]');
        const optionItems = document.querySelectorAll('.custom-option');

        // クリック時にセレクトを開閉
        trigger.addEventListener('click', (e) => {
            e.stopPropagation(); // 他のクリックイベントに干渉しないように
            options.style.display = options.style.display === 'block' ? 'none' : 'block';
        });

        // 選択時クリックで値セット＆表示変更
        optionItems.forEach(option => {
            option.addEventListener('click', (e) => {
                const value = option.getAttribute('data-value');
                trigger.innerHTML = value + '<span class="custom-select-tigger-triangle">▼</span>';
                hiddenInput.value = value;
                options.style.display = 'none';
                e.stopPropagation();
            });
        });

        // 外をクリックしたら閉じる
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.custom-select-wrapper')) {
                options.style.display = 'none';
            }
        });
    });
</script>
@endsection