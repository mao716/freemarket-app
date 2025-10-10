@props(['type' => 'global']) {{-- type: 'simple' or 'global' （プロップ＝引数） --}}

<header class="header {{ $type === 'simple' ? 'header--simple' : 'header--global' }}">
	<div class="header-inner">
		<a href="{{ route('items.index') }}" class="header-logo" aria-label="トップへ">
			<img src="{{ asset('images/logo.svg') }}" alt="COACHTECH ロゴ">
		</a>

		@if ($type === 'global')
		<form class="header-search" method="GET" action="{{ route('items.index') }}">
			<input class="search-input" type="search" name="keyword"
				value="{{ request('keyword') }}"
				placeholder="なにをお探しですか？" autocomplete="off">
		</form>

		<nav class="header-nav" aria-label="グローバル">
			@auth
			<form action="{{ route('logout') }}" method="POST" class="inline-form">
				@csrf
				<button type="submit" class="header-link logout-link">ログアウト</button>
			</form>
			<a href="{{ route('mypage.profile') }}" class="header-link">マイページ</a>
			<a href="{{ route('sell.show') }}" class="header-button">出品</a>
			@else
			<a href="{{ route('login') }}" class="header-link">ログイン</a>
			<a href="{{ route('register') }}" class="header-link">会員登録</a>
			@endauth
		</nav>
		@endif
	</div>
</header>
