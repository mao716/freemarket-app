@extends('layouts.base')

@section('headerType', 'simple')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/verify-email.css') }}">
@endpush

@section('content')
<main class="layout-narrow verify-email-page">
	<section class="page-section">
		<p class="main-message">
			登録していただいたメールアドレスに認証メールを送付しました。<br>
			メール認証を完了してください。
		</p>

		<div class="verify-actions">
			<button class="verify-button" disabled>
				認証はこちらから
			</button>
		</div>

		<form method="POST" action="{{ route('verification.send') }}" class="resend-form">
			@csrf
			<button type="submit" class="link-resend">
				認証メールを再送する
			</button>
		</form>

		<form method="POST" action="{{ route('logout') }}" class="logout-form">
			@csrf
			<button type="submit" class="verify-button">
				ログアウトする
			</button>
		</form>
	</section>
</main>
@endsection
