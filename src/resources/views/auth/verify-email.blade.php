@extends('layouts.base')

@section('headerType', 'simple')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/verify-email.css') }}">
@endpush

@section('content')

@php
$user = auth()->user();

$verifyUrl = route('verification.verify', [
'id' => $user->id,
'hash' => sha1($user->email),
]);
@endphp

<main class="layout-narrow verify-email-page">
	<section class="page-section">
		<p class="main-message">
			登録していただいたメールアドレスに認証メールを送付しました。<br>
			メール認証を完了してください。
		</p>

		<div class="verify-actions">
			<a href="https://mailtrap.io/inboxes"
				class="verify-button"
				target="_blank"
				rel="noopener">
				認証はこちらから
			</a>
		</div>

		<form method="POST" action="{{ route('verification.send') }}" class="resend-form">
			@csrf
			<button type="submit" class="link-resend">
				認証メールを再送する
			</button>
		</form>
	</section>
</main>
@endsection
