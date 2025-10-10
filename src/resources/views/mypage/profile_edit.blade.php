@extends('layouts.app')
@section('title', 'プロフィール設定')

@section('content')
<section class="page-section layout-narrow">
	<h1 class="page-title">プロフィール設定	</h1>

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
			<p class="error">{{ $message }}</p> {{-- バリデーション（入力チェック）エラー表示 --}}
			@enderror
		</div>

		<div class="form-row">
			<label class="form-label" for="postal_code">郵便番号</label>
			<input id="postal_code" name="postal_code" type="text" class="input"
				value="{{ old('postal_code', $user->postal_code ?? '') }}"
				inputmode="numeric" placeholder="123-4567" maxlength="8" pattern="\d{3}-\d{4}">
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

		{{-- プロフィール画像 --}}
		<div class="form-row">
			<label class="form-label" for="avatar">プロフィール画像 <span aria-hidden="true">（任意 / jpeg・png）</span></label>
			@if(!empty($user?->avatar_path))
			{{-- 既存画像のプレビュー（プレビュー＝現在の画像を小さく表示） --}}
			<div style="margin: 6px 0;">
				<img src="{{ \Illuminate\Support\Str::startsWith($user->avatar_path, ['http://', 'https://'])
                    ? $user->avatar_path
                    : asset($user->avatar_path) }}"
					alt="現在のプロフィール画像" style="max-width: 120px; height: auto;">
			</div>
			@endif
			<input id="avatar" name="avatar" type="file" accept="image/jpeg,image/png" class="input">
			@error('avatar')
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

	{{-- 戻り導線（導線＝次に進むためのリンクやボタン） --}}
	<p class="page-note" style="text-align:center;">
		<a href="{{ route('mypage.profile') }}" class="page-note-link">マイページへ戻る</a>
	</p>
</section>
@endsection
