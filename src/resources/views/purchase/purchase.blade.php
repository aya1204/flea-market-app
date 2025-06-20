<!-- 商品購入画面 -->
@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/purchase/purchase.css') }}">
@endsection

@section('content')

@if (session('success'))
<div class="alert-success">
    {{ session('success') }}
</div>
@endif

<div class="purchase-page">
    <div class="purchase-content">
        <div class="left-column">
            <div class="item_row">
                <img class="item-image" src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->title }}">
                <div class="item_name_price">
                    <h2 class="item-title">{{ $item->title }}</h2>
                    <p class="price">¥<span class="item_price">{{ number_format($item->price) }}</span></p>
                </div>
            </div>
            <div class="paymentmethod_row">

                @php
                $selected_paymentmethod_id = request('paymentmethod_id');
                @endphp

                <form action="{{ route('purchase.index', ['item' => $item->id]) }}" method="GET">
                    <div class="paymentmethod_select">
                        <label class="paymentmethod" for="paymentmethod_id">支払い方法
                            <select name="paymentmethod_id" id="paymentmethod_id" class="form_select" onchange="this.form.submit()">
                                <option value="">選択してください</option>
                                @foreach ($paymentmethods as $method)
                                <option value="{{ $method->id }}" {{ $selected_paymentmethod_id == $method->id ? 'selected' : '' }}>
                                    {{ $method->name }}
                                </option>
                                @endforeach
                            </select>
                        </label>
                    </div>
                </form>
            </div>
            <div class="delivery_address">
                <div class="delivery_address_header">
                    <h3 class="delivery_address_title">配送先</h3>
                    <a class="change_link" href="{{ route('purchase.address', ['item' => $item->id]) }}">変更する</a>
                </div>
                <div class="delivery_info">
                    <p class="delivery_info_title">〒{{ $user->postal_code }}</p>
                    <p class="delivery_info_title">{{ $user->address }}</p>
                    @if ($user->building)
                    <p class="delivery_info_title">{{ $user->building }}</p>
                    @else
                    <p class="delivery_info_title">建物名が登録されていません。</p>
                    @endif
                </div>
            </div>
        </div>
        <div class="right-column">
            <div class="purchases_table">
                <div class="row_box">
                    <p class="purchases_table_item_price_title">商品代金</p>
                    <p class="purchases_table_price">¥
                        <span class="purchases_table_item_price">{{ number_format($item->price) }}</span>
                    </p>
                </div>
                <div class="row_box">
                    <p class="purchases_table_paymentmethod_title">支払い方法</p>
                    <p class="selected-method">{{ $method_name ?? '選択してください' }}</p>
                </div>
            </div>
            @if (!$item->is_sold)
            <form action="{{ route('purchase.create', ['item' => $item->id]) }}" method="POST" id="purchase-form">
                @csrf
                {{-- hiddenに確実にIDを入れる --}}
                <input type="hidden" name="paymentmethod_id" value="{{ $selected_paymentmethod_id }}">
                <input type="hidden" name="postal_code" value="{{ old('postal_code', $user->postal_code) }}">
                <input type="hidden" name="address" value="{{ old('address', $user->address) }}">
                <input type="hidden" name="building" value="{{ old('building', $user->building) }}">

                <button type="submit" class="purchase_button" id="purchase-button" {{ $selected_paymentmethod_id ? '' : 'disabled' }}>
                    購入する
                </button>
            </form>
            @else
            <p class="sold-text">Sold</p>
            @endif
            @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@yield('js')

@section('js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('purchase-form');
        const button = document.getElementById('purchase-button');
        const select = document.getElementById('paymentmethod_id');

        // 初期状態の切り替え
        toggleButton(select.value);

        select.addEventListener('change', function() {
            toggleButton(this.value);
        });

        function toggleButton(value) {
            if (!value) {
                button.disabled = true;
                button.classList.add('disabled');
            } else {
                button.disabled = false;
                button.classList.remove('disabled');
            }
        }

        form.addEventListener('submit', function() {
            button.disabled = true;
            button.classList.add('disabled');
        });
    });
</script>

@endsection