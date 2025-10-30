@extends('layouts.app')
@section('title', 'プロフィール設定')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/profile-edit.css') }}">
@endpush

@section('content')
<section class="page-section layout-narrow">
	<h1 class="page-title">プロフィール設定 </h1>

	<div class="form-row" style="text-align:center">
		<div class="form-row avatar-wrapper">
			<div class="avatar">
				<img id="avatarPreview"
					class="avatar-img"
					src="{{ old('avatar_path', $user->avatar_path ?? asset('images/image-placeholder.png')) }}"
					alt="プロフィール画像のプレビュー">
			</div>

			<label for="avatar" class="button-outline" style="margin-top:8px;">画像を選択する</label>
			<input id="avatar"
				name="avatar"
				type="file"
				class="visually-hidden" {{-- 画面には出さずラベルで操作（アクセシビリティ＝操作しやすさ） --}}
				accept="image/jpeg,image/png">

			<p id="avatarError" class="error" style="display:none;"></p>
		</div>
	</div>

	@push('scripts')
	<script src="{{ asset('js/profile.js') }}" defer></script>
	@endpush

	<form method="POST"
		action="{{ route('mypage.save') }}" {{-- 送信先ルート（コントローラの保存処理） --}}
		enctype="multipart/form-data" {{-- 画像アップ用のエンコーディング（ファイル送信用設定） --}}
		class="form">
		@csrf
		<div class="form-row">
			<label class="form-label" for="name">ユーザー名</label>
			<input id="name" name="name" type="text" class="input"
				value="{{ old('name', $user->name ?? '') }}" maxlength="20">
			@error('name')
			<p class="error">{{ $message }}</p>
			@enderror
		</div>

		<div class="form-row">
			<label class="form-label" for="postal_code">郵便番号</label>
			<input id="postal_code" name="postal_code" type="text" class="input"
				value="{{ old('postal_code', $user->postal_code ?? '') }}"
				inputmode="numeric" placeholder="123-4567" maxlength="8">
			@error('postal_code')
			<p class="error">{{ $message }}</p>
			@enderror
		</div>

		<div class="form-row">
			<label class="form-label" for="address">住所</label>
			<input id="address" name="address" type="text" class="input"
				value="{{ old('address', $user->address ?? '') }}"
				placeholder="例）東京都〇〇区…">
			@error('address')
			<p class="error">{{ $message }}</p>
			@enderror
		</div>

		<div class="form-row">
			<label class="form-label" for="building">建物名</label>
			<input id="building" name="building" type="text" class="input"
				value="{{ old('building', $user->building ?? '') }}"
				placeholder="例）コーポ〇〇 101号室">
			@error('building')
			<p class="error">{{ $message }}</p>
			@enderror
		</div>

		{{-- 送信ボタン --}}
		<div class="form-row form-row--actions">
			<button type="submit" class="button button-primary button-full">
				{{ isset($isFirstSetup) && $isFirstSetup ? '設定を保存する' : '更新する' }}
			</button>
		</div>
	</form>

</section>
@endsection
