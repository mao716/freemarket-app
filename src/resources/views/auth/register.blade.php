@extends('layouts.guest')
@section('title', '会員登録')

@section('content')
<section class="page-section">
	<h1 class="page-title">会員登録</h1>

	<form method="POST" action="{{ route('register') }}" class="form">
		@csrf

		<div class="form-row">
			<label for="name" class="form-label">
				<span>ユーザー名</span>
			</label>
			<input id="name" type="text" name="name" value="{{ old('name') }}" required maxlength="20" class="input">
			@error('name') <p class="error">{{ $message }}</p> @enderror
		</div>

		<div class="form-row">
			<label for="email" class="form-label">
				<span>メールアドレス</span>
			</label>
			<input id="email" type="email" name="email" value="{{ old('email') }}" required class="input">
			@error('email') <p class="error">{{ $message }}</p> @enderror
		</div>

		<div class="form-row">
			<label for="password" class="form-label">
				<span>パスワード</span>
			</label>
			<input id="password" type="password" name="password" required minlength="8" autocomplete="new-password" class="input">
			@error('password') <p class="error">{{ $message }}</p> @enderror
		</div>

		<div class="form-row">
			<label for="password_confirmation" class="form-label">
				<span>確認用パスワード</span>
			</label>
			<input id="password_confirmation" type="password" name="password_confirmation" required minlength="8" autocomplete="new-password" class="input">
		</div>

		<div class="form-row form-row--actions">
			<button type="submit" class="button button-primary button-full">登録する</button>
		</div>
	</form>


	<p class="page-note">
		<a class="page-note-link" href="{{ route('login') }}">ログインはこちら</a>
	</p>

</section>
@endsection
