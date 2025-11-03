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
                    <label class="birth_year" for="birth_year">生年月日</label>
                    <select name="birth_year" id="birth_year" class="birth_year">
                        @for ($year = now()->year; $year >= 1900; $year--)
                        <option value="{{ $year }}" {{ old('birth_year', Auth::user()->birth_year) == $year ? 'selected' : ''}}>{{ $year }}</option>
                        @endfor
                    </select>年
                    <select name="birth_month" id="birth_month" class="birth_month">
                        @for ($month = 1; $month <= 12; $month++)
                            <option value="{{ $month }}" {{ old('birth_month', Auth::user()->birth_month) == $month ? 'selected' : ''}}>{{ $month }}</option>
                            @endfor
                    </select>月
                    <select name="birth_day" id="birth_day" class="birth_day">
                        @for ($day = 1; $day <= 31; $day++)
                            <option value="{{ $day }}" {{ old('birth_day', Auth::user()->birth_day) == $day ? 'selected' : ''}}>{{ $day }}</option>
                            @endfor
                    </select>日
                    <!-- <input id="birthday_year" type="text" name="postal_code" value="{{ old('postal_code', Auth::user()->postal_code) }}" class="text-box"> -->
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