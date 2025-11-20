<!-- 取引チャット画面 message.blade.php -->
@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/transaction/message.css') }}">
@endsection

@section('content')
<div class="transaction-container">
    <!-- 左側：取引リスト -->
    <div class="transaction-list">
        <h2 class="list-title">その他の取引</h2>
        @forelse ($otherTransactions as $otherTransaction)
        {{-- リスト内の個別の取引データ --}}
        <a href="{{ route('transaction.show', $otherTransaction->id) }}" class="item-card-link">
            <div class="other-item-card">
                {{-- 商品タイトルは $otherTransaction->item->titleから取得 --}}
                <h5 class="other-title-header">
                    <span class="other-item-title">{{ $otherTransaction->item->title }}</span>
                </h5>

                @php
                $transaction = $item->transaction ?? null;
                $unreadCount = $otherTransaction->unreadCountForUser(auth()->id());
                @endphp
            </div>
        </a>
        @empty
        <p class="no-other-transactions">他に取引中の商品はありません。</p>
        @endforelse
    </div>

    <!-- 右側：取引エリア全体 -->
    <div class="transaction-area">
        <div class="profile-form">
            @if(Auth::user()->image)
            <img id="preview" src="{{ asset('storage/images/' . Auth::user()->image) }}" alt="プロフィール画像" class="profile-image">
            @else
            <img id="preview" src="{{ asset('storage/images/default_user_icon.png') }}" alt="デフォルト画像" class="profile-image">
            @endif

            <!-- 相手のユーザー名を表示 -->
            @php
            $otherUser = $transaction->seller_user_id === auth()->id()
            ? $transaction->purchase
            : $transaction->seller;
            @endphp
            <h2 class="transaction-title">
                「{{ $otherUser->name }}」さんとの取引画面

                {{-- 購入者が未評価の場合のみボタン表示 --}}
                @if ($isBuyerLoggedIn && $transaction->status !== 'completed')
                <button type="button" id="open-rating-modal" class="finish-button">
                    <p class="finish-button-text">取引を完了する</p>
                </button>
                @endif
            </h2>
        </div>

        <div class="chat-area">
            @if($transaction)
            <!-- 商品情報ヘッダー -->
            <div class="item-header">
                <img src="{{ asset('storage/' . $transaction->item->image) }}" class="item-image">
                <div class="header-info">
                    <h2 class="item-title">{{ $transaction->item->title }}</h2>
                    <p class="item-price">¥{{ number_format($transaction->item->price) }}</p>
                </div>
            </div>

            <!-- メッセージ表示エリア -->
            <div class="messages-container">
                @forelse($transaction->messages->reverse() as $message)
                <div class="sent-messages {{ $message->user_id === auth()->id() ? 'my-message' : 'other-message' }}">
                    <div class="message-user">
                        <img src="{{ asset('storage/images/' . ($message->user->image ?? 'default_user_icon.png')) }}" alt="{{ $message->user->name }}" class="user-icon">
                        <span class="user-name">{{ $message->user->name }}</span>
                    </div>

                    <div class="message-content">
                        <p class="transaction-message">{{ $message->message }}</p>
                        @if ($message->image)
                        <img src="{{ asset('storage/' . $message->image) }}" alt="添付画像" class="message-image">
                        @endif
                    </div>

                    @if ($message->user_id === auth()->id())
                    <div class="message-actions">
                        <button class="edit-btn" data-message-id="{{ $message->id }}">編集</button>
                        <form class="edit-form" action="{{ route('transaction.message.update', $message->id) }}" method="POST" style="display:none;">
                            @csrf
                            @method('PUT')
                            <input type="text" name="message" value="{{ $message->message }}">
                            <button type="submit">保存</button>
                            <button type="button" class="cancel-edit">キャンセル</button>
                        </form>
                        <form action="{{ route('transaction.message.delete', $message->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="delete-btn">削除</button>
                        </form>
                    </div>
                    @endif
                </div>
                @empty
                <p class="no-messages">まだメッセージはありません</p>
                @endforelse
            </div>

            <!-- メッセージ送信フォーム -->
            @if ($errors->any())
                <div class="alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li class="alert-danger-message">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form action="{{ route('transaction.message.send', $transaction->id) }}" method="POST" class="message-form" enctype="multipart/form-data">
                @csrf
                <textarea id="message-input" name="message" placeholder="取引メッセージを入力してください" rows="3" class="message-input">{{ old('message') }}</textarea>

                <label class="add-image">
                    <input class="add-image-text" type="file" name="image" style="display: none;">
                    画像を追加
                </label>
                <button type="submit" class="sent-button"></button>
            </form>
        </div>
        @else
        <div class="no-transaction-selected">取引を選択してください</div>
        @endif
    </div>
</div>

<!-- 評価モーダル -->
<div id="rating-modal" class="modal-overlay" style="display: none;">
    <div class="modal-content">
        <h3 class="modal-message">取引が完了しました。</h3>
        <p class="modal-questions">今回の取引相手はどうでしたか？</p>

        <form action="{{ route('transaction.review.store', $transaction->id) }}" method="POST" class="review-form">
            @csrf
            <div class="rating">
                @for ($i = 5; $i >= 1; $i--)
                <input type="radio" id="star{{$i}}" name="rating" value="{{$i}}" required>
                <label for="star{{$i}}" title="{{$i}}つ星">★</label>
                @endfor
            </div>
            <button type="submit" class="submit-rating">送信する</button>
        </form>
    </div>
</div>
@endsection

@section('js')
<script>
    document.addEventListener('DOMContentLoaded', function() {

        // ① 本文を書いて他のページへ遷移しても保持
        const textarea = document.getElementById('message-input');
        // 取引IDを安全にJavaScript文字列として取得
        const transactionId = "{{ $transaction->id ?? '' }}";
        // 取引ID + ログイン中のユーザーID を key にする
        const userId = "{{ auth()->id() }}";
        const storageKey = 'draft_message_' + userId + '_' + transactionId;

        // PHPの old('message') の値を安全に取得（空の場合は ' ' に展開される）
        const oldMessageValue = "{{ old('message') }}";

        if (textarea) {
            const savedMessage = localStorage.getItem(storageKey);

            // 【修正】 old() が空文字列（トリム後）かつ localStorageにデータがある場合のみ復元
            if (savedMessage && oldMessageValue.trim() === '') {
                // textarea の現在の値（old('message') の値）が空の場合のみ上書き
                if (textarea.value.trim() === '') {
                    textarea.value = savedMessage;
                }
            }

            // 入力が変わるたびに保存
            textarea.addEventListener('input', function() {
                localStorage.setItem(storageKey, textarea.value);
            });

            // フォーム送信時に削除
            const form = textarea.closest('form');
            if (form) {
                form.addEventListener('submit', function() {
                    localStorage.removeItem(storageKey);
                });
            }
        }

        // ②③ 送信済みのメッセージ編集/キャンセル処理 (既存のロジックは省略)
        const editButtons = document.querySelectorAll('.edit-btn');
        editButtons.forEach(button => {
            button.addEventListener('click', function() {
                const messageDiv = button.closest('.sent-messages');
                const messageContent = messageDiv.querySelector('.message-content');
                const editForm = messageDiv.querySelector('.edit-form');
                messageContent.style.display = 'none';
                editForm.style.display = 'block';
            });
        });

        const cancelButtons = document.querySelectorAll('.cancel-edit');
        cancelButtons.forEach(button => {
            button.addEventListener('click', function() {
                const messageDiv = button.closest('.sent-messages');
                const messageContent = messageDiv.querySelector('.message-content');
                const editForm = messageDiv.querySelector('.edit-form');
                editForm.style.display = 'none';
                messageContent.style.display = 'block';
            });
        });

        // ④ モーダル開閉処理
        const openModalButton = document.getElementById('open-rating-modal');
        const modal = document.getElementById('rating-modal');

        if (openModalButton) {
            openModalButton.addEventListener('click', function() {
                modal.style.display = 'block';
            });
        }

        // 🚨 修正後の正しい Blade 構文
        const isSeller = @json($isSeller);
        const buyerHasReviewed = @json($buyerHasReviewed);
        const sellerHasReviewed = @json($sellerHasReviewed);

        if (isSeller && buyerHasReviewed && !sellerHasReviewed) {
            if (modal) {
                modal.style.display = 'block';
            }
        }

        if (modal) {
            // モーダル外クリックで閉じる
            modal.addEventListener('click', function(event) {
                if (event.target.classList.contains('modal-overlay')) {
                    modal.style.display = 'none';
                }
            });
        }
    });
</script>
@endsection