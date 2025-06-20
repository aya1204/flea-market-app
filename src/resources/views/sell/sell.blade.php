<!-- 出品画面 -->
@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/sell/sell.css') }}">
@endsection

@if ($errors->any())
<div class="alert-danger">
    <ul>
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

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
                <img id="image-preview" src="" alt="画像プレビュー" class="image-preview">
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
                <label for="condition_id" class="condition">商品の状態</label>
                <div class="custom-select-wrapper">
                    <div class="custom-select-trigger">商品の状態を選択してください
                        <span class="custom-select-trigger-triangle">▼</span>
                    </div>
                    <div class="custom-options">
                        <div class="custom-option" data-value="1">良好</div>
                        <div class="custom-option" data-value="2">目立った傷や汚れなし</div>
                        <div class="custom-option" data-value="3">やや傷や汚れあり</div>
                        <div class="custom-option" data-value="4">状態が悪い</div>
                    </div>
                    <input type="hidden" name="condition_id" id="condition_id" value="{{ old('condition_id') }}">
                </div>
            </div>
        </div>

        <!-- 商品名と説明 -->
        <div class="item-group">
            <h3 class="item-group-title">商品名と説明</h3>
            <div class="input-group">
                <div class="name-group">
                    <label for="title" class="title">商品名</label>
                    <input type="text" id="title" name="title" class="text-box" value="{{ old('title') }}">
                </div>

                <div class="brand-group">
                    <label for="brand_id" class="brand">ブランド名</label>
                    <input type="text" id="brand_id" name="brand_id" class="text-box">
                </div>

                <div class="description-group">
                    <label for="description" class="description">商品の説明</label>
                    <textarea id="description" name="description" rows="6" class="textarea"></textarea>
                </div>

                <div class="price-group">
                    <label for="price" class="price">販売価格</label>
                    <div class="price-wrapper">
                        <input type="text" id="price" name="price" placeholder="¥" class="price-text-box">
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
        // セレクトボックスの全体のラッパー要素を取得
        const wrapper = document.querySelector('.custom-select-wrapper');
        // 商品の状態を選択してくださいの表示する部分
        const trigger = document.querySelector('.custom-select-trigger');
        // 表示される選択肢
        const options = document.querySelector('.custom-options');
        // 実際にフォームで送信される非表示の入力項目
        const hiddenInput = document.querySelector('input[name="condition_id"]');
        // 全ての選択肢（ < div class = "custom-option" > ）を配列として取得
        const optionItems = document.querySelectorAll('.custom-option');

        // クリック時にセレクトを開閉
        trigger.addEventListener('click', (e) => {
            e.stopPropagation(); // 他のクリックイベントを止める
            options.style.display = options.style.display === 'block' ? 'none' : 'block';
        });

        // 選択時クリックで値セット＆表示変更
        optionItems.forEach(option => {
            option.addEventListener('click', (e) => {
                const value = option.getAttribute('data-value'); // 選ばれたvalueを取得
                const text = option.textContent.trim(); // 表示用のテキストを取得
                trigger.innerHTML = text + '<span class="custom-select-trigger-triangle">▼</span>'; // 表示更新
                hiddenInput.value = value; // 選択値をhiddenにセット
                options.style.display = 'none'; // 選択肢を閉じる
                e.stopPropagation();
            });
        });

        // 外をクリックしたら閉じる
        document.addEventListener('click', (e) => {
            // custom-select-wrapper以外をクリックしたら閉じる
            if (!e.target.closest('.custom-select-wrapper')) {
                options.style.display = 'none';
            }
        });

        // アップロード欄、プレビュー表示、ラベルを取得
        const imageInput = document.getElementById('image');
        const preview = document.getElementById('image-preview');
        const label = document.querySelector('.image-label');

        // 画像が選ばれたときの処理
        imageInput.addEventListener('change', function(e) {
            const file = e.target.files[0]; // ファイル選択イベント。fileは1番最初に選んだ画像
            if (file && file.type.startsWith('image/')) {
                const reader = new FileReader(); // 画像ファイルかどうかチェック→読み込み開始
                reader.onload = function(event) {
                    preview.src = event.target.result;
                    preview.style.display = 'block';
                    label.classList.add('hidden'); // 読み込み完了後、プレビュー表示と画像を選択するラベルを非表示
                };
                reader.readAsDataURL(file);
            } else {
                preview.src = '';
                preview.style.display = 'none';
                label.classList.remove('hidden'); // 画像じゃなかった場合は選択ボタンを再表示
            }
        });
    });
</script>
@endsection