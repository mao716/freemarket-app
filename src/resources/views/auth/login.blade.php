@extends('layouts.base')
@section('title', 'ログイン')

@section('content')
<main class="layout-narrow">
	<section class="page-section">
		<h1 class="page-title">ログイン</h1>

		<form method="POST" action="{{ route('login') }}" class="form">
			@csrf

			<div class="form-row">
				<label for="email" class="form-label">
					<span>メールアドレス</span>
				</label>
				<input id="email" type="email" name="email" value="{{ old('email') }}" class="input">
				@error('email') <p class="error">{{ $message }}</p> @enderror
			</div>

			<div class="form-row">
				<label for="password" class="form-label">
					<span>パスワード</span>
				</label>
				<input id="password" type="password" name="password" class="input">
				@error('password') <p class="error">{{ $message }}</p> @enderror
			</div>

			<div class="form-row form-row--actions">
				<button type="submit" class="button button-primary button-full">ログインする</button>
			</div>
		</form>

		<p class="page-note">
			<a class="page-note-link" href="{{ route('register') }}">会員登録はこちら</a>
		</p>
	</section>
</main>
@endsection
