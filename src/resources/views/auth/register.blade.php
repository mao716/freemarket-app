@extends('layouts.app')
@section('title', '会員登録')

@section('content')
<section class="p-auth">
	<h1 class="p-auth__title">会員登録</h1>

	@if ($errors->any())
	<div class="c-alert c-alert--danger">
		<ul class="c-list c-list--disc">
			@foreach ($errors->all() as $error)
			<li>{{ $error }}</li>
			@endforeach
		</ul>
	</div>
	@endif

	<form method="POST" action="{{ route('register') }}" class="c-form">
		@csrf

		<label class="c-form__row">
			<span class="c-form__label">ユーザー名</span>
			<input id="name" type="text" name="name" value="{{ old('name') }}" required maxlength="20" class="c-input">
			@error('name') <p class="c-error">{{ $message }}</p> @enderror
		</label>

		<label class="c-form__row">
			<span class="c-form__label">メールアドレス</span>
			<input id="email" type="email" name="email" value="{{ old('email') }}" required class="c-input">
			@error('email') <p class="c-error">{{ $message }}</p> @enderror
		</label>

		<label class="c-form__row">
			<span class="c-form__label">パスワード（8文字以上）</span>
			<input id="password" type="password" name="password" required minlength="8" autocomplete="new-password" class="c-input">
			@error('password') <p class="c-error">{{ $message }}</p> @enderror
		</label>

		<label class="c-form__row">
			<span class="c-form__label">確認用パスワード</span>
			<input id="password_confirmation" type="password" name="password_confirmation" required minlength="8" autocomplete="new-password" class="c-input">
		</label>

		<button type="submit" class="c-button c-button--primary">登録する</button>
	</form>

	<p class="p-auth__switch">ログインは <a href="{{ route('login') }}">こちら</a></p>
</section>
@endsection
