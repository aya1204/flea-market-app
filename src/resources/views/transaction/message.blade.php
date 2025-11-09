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
        @foreach ($items as $item)
        <a href="{{ route('transaction.show', $item->transaction->id) }}" class="item-card-link">
            <div class="other-item-card">
                <h5 class="other-title-header">
                    <span class="other-item-title">{{ $item->title }}</span>
                </h5>

                @php
                $transaction = $item->transaction ?? null;
                $unreadCount = $transaction ? $transaction->unreadCountForUser(auth()->id()) : 0;
                @endphp
            </div>
        </a>
        @endforeach
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
                <button type="button" id="open-rating-modal" class="finish-button">取引を完了する</button>
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
            <form action="{{ route('transaction.message.send', $transaction->id) }}" method="POST" class="message-form" enctype="multipart/form-data">
                @csrf
                <textarea id="message-input" name="message" placeholder="メッセージを入力" rows="3"></textarea>
                @if ($errors->has('message'))
                <div class="alert-danger">{{ $errors->first('message') }}</div>
                @endif

                <label class="add-image">
                    <input type="file" name="image" style="display: none;">
                    @if ($errors->has('image'))
                    <div class="alert-danger">{{ $errors->first('image') }}</div>
                    @endif
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
        // ①本文を書いて他のページへ遷移しても保持
        const textarea = document.getElementById('message-input');
        const transactionId = "{{ $transaction->id ?? '' }}";
        const storageKey = 'draft_message_' + transactionId;

        // ページ読み込み時にlocalStorageから復元
        if (textarea && localStorage.getItem(storageKey)) {
            textarea.value = localStorage.getItem(storageKey);
        }

        // 入力が変わるたびに保存
        if (textarea) {
            textarea.addEventListener('input', function() {
                localStorage.setItem(storageKey, textarea.value);
            });

            // 送信時にlocalStorageを削除
            const form = textarea.closest('form');
            form.addEventListener('submit', function() {
                localStorage.removeItem(storageKey);
            });
        }

        // ②送信済みのメッセージ編集フォーム
        const editButtons = document.querySelectorAll('.edit-btn');
        editButtons.forEach(button => {
            button.addEventListener('click', function() {
                const messageDiv = button.closest('.sent-messages');
                const messageContent = messageDiv.querySelector('.message-content');
                const editForm = messageDiv.querySelector('.edit-form');

                // 要素をコンソールに表示
                // console.log('MessageDiv:', messageDiv);
                // console.log('MessageContent:', messageContent);
                // console.log('EditForm:', editForm);

                // メッセージコンテンツを非表示にし、編集フォームを表示する
                messageContent.style.display = 'none';
                editForm.style.display = 'block';
            });
        });

        // ③キャンセルボタンの処理
        const cancelButtons = document.querySelectorAll('.cancel-edit');
        cancelButtons.forEach(button => {
            button.addEventListener('click', function() {
                const messageDiv = button.closest('.sent-messages');
                // それぞれそのメッセージの内容部分と編集フォームを取得
                const messageContent = messageDiv.querySelector('.message-content');
                const editForm = messageDiv.querySelector('.edit-form');

                // 編集フォームを隠してメッセージコンテンツを再表示する
                editForm.style.display = 'none';
                messageContent.style.display = 'block';
            });
        });

        // ④モーダル開閉処理
        const openModalButton = document.getElementById('open-rating-modal');
        const modal = document.getElementById('rating-modal');

        if (openModalButton) {
            openModalButton.addEventListener('click', function() {
                modal.style.display = 'block';
            });
        }

        // 出品者：購入者が評価済みなら自動でモーダル表示
        const isSeller = {{ $isSeller ? 'true' : 'false'}};
        const buyerHasReviewed = {{ $buyerHasReviewed ? 'true' : 'false' }};
        const sellerHasReviewed = {{ $sellerHasReviewed ? 'true' : 'false' }};

        if (isSeller && buyerHasReviewed && !sellerHasReviewed) {
            modal.style.display = 'block';
        }

        // モーダル外クリックで閉じる
        modal.addEventListener('click', function(event) {
            if (event.target.classList.contains('modal-overlay')) {
                modal.style.display = 'none';
            }
        });
    });
</script>
@endsection