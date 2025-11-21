@extends('layouts.base')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/profile-edit.css') }}">
@endpush

@section('title', 'プロフィール設定')

@section('content')
<main class="layout-narrow">
	<section class="page-section">
		<h1 class="page-title">プロフィール設定</h1>
		<form method="POST"
			action="{{ route('mypage.save') }}"
			enctype="multipart/form-data"
			class="form">
			@csrf
			<div class="form-row">
				<div class="avatar-wrapper">
					<div class="avatar">
						<img
							id="avatarPreview"
							class="avatar-img"
							src="{{ $user?->avatar_path ? Storage::url($user->avatar_path) : asset('images/image-placeholder.png') }}">
					</div>

					<label for="avatar" class="button-outline">画像を選択する</label>
					<input id="avatar"
						name="avatar"
						type="file"
						class="visually-hidden"
						accept="image/jpeg,image/png">
					@error('avatar') <p class="error">{{ $message }}</p> @enderror
					<p id="avatarError" class="error" style="display:none;"></p>
				</div>
			</div>

			<div class="form-row">
				<label class="form-label" for="name">ユーザー名</label>
				<input id="name" name="name" type="text" class="input"
					value="{{ old('name', $user->name ?? '') }}" maxlength="20">
				@error('name') <p class="error">{{ $message }}</p> @enderror
			</div>

			<div class="form-row">
				<label class="form-label" for="postal_code">郵便番号</label>
				<input id="postal_code" name="postal_code" type="text" class="input"
					value="{{ old('postal_code', $user->postal_code ?? '') }}"
					inputmode="numeric" maxlength="8">
				@error('postal_code') <p class="error">{{ $message }}</p> @enderror
			</div>

			<div class="form-row">
				<label class="form-label" for="address">住所</label>
				<input id="address" name="address" type="text" class="input"
					value="{{ old('address', $user->address ?? '') }}">
				@error('address') <p class="error">{{ $message }}</p> @enderror
			</div>

			<div class="form-row">
				<label class="form-label" for="building">建物名</label>
				<input id="building" name="building" type="text" class="input"
					value="{{ old('building', $user->building ?? '') }}">
				@error('building') <p class="error">{{ $message }}</p> @enderror
			</div>

			<div class="form-row form-row--actions">
				<button type="submit" class="button button-primary button-full">更新する</button>
			</div>
		</form>
	</section>
</main>
@endsection

@push('scripts')
<script src="{{ asset('js/profile.js') }}" defer></script>
@endpush
