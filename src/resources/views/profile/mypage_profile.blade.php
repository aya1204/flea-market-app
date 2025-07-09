<!-- プロフィール設定画面 -->
@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/profile/mypage_profile.css') }}">
@endsection

@section('content')


<div class="profile-container">
    <h2 class="profile-title">プロフィール設定</h2>
    <div class="profile-detail">
        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="profile_form">
            @csrf
            <!-- 左側：画像表示 -->
            <div class="profile-image_form">
                @if(Auth::user()->image)
                <img id="preview" src="{{ asset('storage/images/' . Auth::user()->image) }}" alt="プロフィール画像" class="profile-image">
                @else
                <img id="preview" src="{{ asset('storage/images/default_user_icon.png') }}" alt="デフォルト画像" class="profile-image">
                @endif

                <!-- 右側：画像を選択する表示 -->
                <label for="image" class="user-icon_select">画像を選択する</label>
                <input type="file" id="image" name="image" accept="image/*" class="hidden-file-input">
            </div>

            <div class="profile-detail_form">
                <div class="profile">
                    <label class="name" for="name">ユーザー名</label>
                    <input type="text" name="name" value="{{ old('name', Auth::user()->name) }}" class="text-box">
                </div>
                <div class="profile">
                    <label class="postal_code" for="postal_code">郵便番号</label>
                    <input id="postal_code" type="text" name="postal_code" value="{{ old('postal_code', Auth::user()->postal_code) }}" class="text-box">
                </div>
                <div class="profile">
                    <label class="address" for="address">住所</label>
                    <input id="address" type="text" name="address" value="{{ old('address', Auth::user()->address) }}" class="text-box">
                </div>
                <div class="profile">
                    <label class="building" for="building">建物名</label>
                    <input id="building" type="text" name="building" value="{{ old('building', Auth::user()->building) }}" class="text-box">
                </div>
                <button type="submit" class="update_button">更新する</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('js')
<!-- JavaScriptによるリアルタイムプレビュー -->
<script>
    document.getElementById('image').addEventListener('change', function(event) {
        const reader = new FileReader();
        const file = event.target.files[0];

        if (file) {
            reader.onload = function(e) {
                document.getElementById('preview').src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    });
</script>
@endsection
