@extends('layouts.app')
@section('title', 'ログイン')

@section('content')
<section class="p-auth">
	<h1 class="p-auth__title">ログイン</h1>

	@if ($errors->any())
	<div class="c-alert c-alert--danger">
		<ul class="c-list c-list--disc">
			@foreach ($errors->all() as $error)
			<li>{{ $error }}</li>
			@endforeach
		</ul>
	</div>
	@endif

	<form method="POST" action="{{ route('login') }}" class="c-form">
		@csrf
		<label class="c-form__row">
			<span class="c-form__label">メールアドレス</span>
			<input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus class="c-input">
			@error('email') <p class="c-error">{{ $message }}</p> @enderror
		</label>

		<label class="c-form__row">
			<span class="c-form__label">パスワード</span>
			<input id="password" type="password" name="password" required autocomplete="current-password" class="c-input">
			@error('password') <p class="c-error">{{ $message }}</p> @enderror
		</label>

		<button type="submit" class="c-button c-button--primary">ログインする</button>
	</form>

	<p class="p-auth__switch">会員登録は <a href="{{ route('register') }}">こちら</a></p>
</section>
@endsection
