<!-- 送付先変更ページ -->
@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/purchase/delivery_address_edit.css') }}">
@endsection

@section('content')

<div class="profile-container">
    <h2 class="delivery_address_edit_title">住所の変更</h2>
    <div class="profile-detail_form">
        <form method="POST" action="{{ route('purchase.update', ['item' => $item->id]) }}" enctype="multipart/form-data">
            @csrf
            <div class="profile">
                <label for="postal_code" class="postal_code">郵便番号</label>
                <input id="postal_code" type="text" name="postal_code" value="{{ old('postal_code', $user->postal_code) }}" class="text-box">
            </div>
            <div class="profile">
                <label for="address" class="address">住所</label>
                <input id="address" type="text" name="address" value="{{ old('address', $user->address) }}" class="text-box">
            </div>
            <div class="profile">
                <label for="building" class="building">建物名</label>
                <input id="building" type="text" name="building" value="{{ old('building', $user->building) }}" class="text-box">
            </div>
            <button type="submit" class="update_button">更新する</button>
        </form>
    </div>
</div>
@endsection