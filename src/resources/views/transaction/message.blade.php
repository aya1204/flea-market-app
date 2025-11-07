<!-- 取引チャット画面 -->
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
            <div class="item-card">
                <h5 class="title-header">
                    <span class="item-title">{{ $item->title }}</span>
                </h5>

                @php
                $transaction = $item->transaction ?? null;
                $unreadCount = $transaction ? $transaction->unreadCountForUser(auth()->id()) : 0;
                @endphp

                @if ($unreadCount > 0)
                <span class="notification-badge">{{ $unreadCount }}</span>
                @endif
            </div>
        </a>
        @endforeach
        {{-- @forelse($transactions as $transaction)
        <a href="{{ route('transaction.show', $transaction->id) }}">
        <div class="item-info">
            <h3 class="item-name">{{ $transaction->item->title }}</h3>
            @php
            $lastMessage = $transaction->messages->first();
            $unread = $transaction->unreadCountForUser(auth()->id());
            @endphp
        </div>

        @if ($unread > 0)
        <span class="badge">{{ $unread }}</span>
        @endif
        </a>
        @empty
        <p class="no-transactions">取引中の商品はありません</p>
        </a>
        @endforelse --}}
    </div>
    <!-- 右側：取引エリア全体 -->
    <div class="transaction-area">
        <div class="profile-image_form">
            @if(Auth::user()->image)
            <img id="preview" src="{{ asset('storage/images/' . Auth::user()->image) }}" alt="プロフィール画像" class="profile-image">
            @else
            <img id="preview" src="{{ asset('storage/images/default_user_icon.png') }}" alt="デフォルト画像" class="profile-image">
            @endif
        </div>
        <!-- 相手のユーザー名を表示 -->
        @php
        $otherUser = $transaction->seller_user_id === auth()->id()
        ? $transaction->purchase
        : $transaction->seller;
        @endphp
        <h2 class="transaction-title">
            「 {{$otherUser->name }} 」さんとの取引画面

            {{-- 購入者が未評価の場合、購入者のみに取引完了ボタン表示 --}}
            @if ($isBuyer && !$buyerHasReviewed)
            <button type="button" id="open-rating-modal" class="finish-button">取引を完了する</button>

            {{-- 購入者が評価済・出品者が未評価の場合、出品者にのみ完了ボタン表示 --}}
            @elseif ($isSeller && $buyerHasReviewed && !$sellerHasReviewed)
            <button type="button" id="open-rating-modal" class="finish-button">取引を完了する</button>

            @endif

        </h2>
    </div>
    <div class="chat-area">
        @if($transaction)
        <!-- 商品情報ヘッダー -->
        <div class="chat-header">
            <img src="{{ asset('storage/' . $transaction->item->image) }}">
            <div class="header-info">
                <h2>{{ $transaction->item->title}}</h2>
                <p>¥{{ number_format($transaction->item->price) }}</p>
            </div>

            <!-- メッセージ表示エリア -->
            <div class="messages-container">
                @forelse($transaction->messages->reverse() as $message)
                <div class="message" {{ $message->user_id === auth()->id() ? 'my-message' : 'other-message'}}>
                    <!-- ユーザーアイコンとユーザー名を横並び -->
                    <div class="message-user">
                        <img src="{{ asset('storage/images/' . ($message->user->image ?? 'default_user_icon.png')) }}" alt="{{ $message->user->name }}" class="user-icon">
                        <span class="user-name">{{ $message->user->name }}</span>
                    </div>

                    <div class="message-content">
                        <p>{{ $message->message }}</p>
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
            </div>
            @empty
            <p class="no-messages">まだメッセージはありません</p>
            @endforelse
        </div>

        <!-- メッセージ送信フォーム -->
        <form action="{{ route('transaction.message.send', $transaction->id) }}" method="POST" class="message-form" enctype="multipart/form-data">
            @csrf

            <!-- バリデーションエラー -->
            <textarea id="message-input" name="message" placeholder="メッセージを入力" rows="3"></textarea>
            @if ($errors->has('message'))
            <div class="alert-danger">
                {{ $errors->first('message') }}
            </div>
            @endif

            <input type="file" name="image" placeholder="画像を追加">
            @if ($errors->has('image'))
            <div class="alert-danger">
                {{ $errors->first('image') }}
            </div>
            @endif
            <button type="submit" class="sent-button">送信</button>
        </form>
        @else
        <div class="no-transaction-selected">取引を選択してください</div>
    </div>
    @endif
</div>
</div>

<div id="rating-modal" class="modal-overlay" style="display: none;">
    <div class="modal-content">
        <h3 class="modal-message">取引が完了しました。</h3>
        <p class="modal-questions">今回の取引相手はどうでしたか？</p>

        <form action="{{ route('transaction.review.store', $transaction->id) }}" method="POST" class="review-form">
            @csrf

            <div class="rating">
                @for ($i = 5; $i >= 1; $i--)
                <input type="radio" id="star{{$i}}" name="rating" value="{{$i}}">
                <label for="star{{$i}}" title="{{$i}}つ星">★</label>
                @endfor
            </div>

            <button type="submit" class="submit-rating">送信する</button>
        </form>
    </div>
</div>

@endsection

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // ①本文を書いて他のページへ遷移しても保持
        const textarea = document.getElementById('message-input');
        const transactionId = "{{ $currentTransaction->id ?? '' }}"; // 取引IDでキーを分ける
        const storageKey = 'draft_message_' + transactionId;

        // ページ読み込み時にlocalStorageから復元
        if (localStorage.getItem(storageKey)) {
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
                const messageDiv = button.closest('.message');
                const messageContent = messageDiv.querySelector('.message-content');
                const editForm = messageDiv.querySelector('.edit-form');

                // 非表示のpを隠してフォームを表示
                messageContent.style.display = 'none';
                editForm.style.display = 'block';
            });
        });

        const cancelButtons = document.querySelectorAll('.cancel-edit');
        cancelButtons.forEach(button => {
            button.addEventListener('click', function() {
                const editForm = button.closest('.edit-form');
                const messageDiv = editForm.closest('.message');
                const messageContent = messageDiv.querySelector('.message-content');

                editForm.style.display = 'none';
                messageContent.style.display = 'block';
            });
        });
        // モーダル開閉処理
        document.getElementById('open-rating-modal').addEventListener('click', function() {
            document.getElementById('rating-modal').style.display = 'block';
        });

        document.getElementById('rating-modal').addEventListener('click', function(event) {
            if (event.target.classList.constains('modal-overlay')) {
                document.getElementById('rating-modal'), style.display = none;
            }
        });
    });
</script>