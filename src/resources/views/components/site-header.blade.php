<header class="c-header">
	<div class="c-header__inner">
		<a href="{{ route('items.index') }}" class="c-header__logo" aria-label="トップへ">
			<img src="{{ asset('images/logo.svg') }}" alt="サイトロゴ" class="c-header__logoImage">
		</a>

		<nav class="c-header__nav">
			@auth
			<a href="{{ route('items.index') }}" class="c-header__link">トップ</a>
			<a href="{{ route('mypage.index') }}" class="c-header__link">マイページ</a>
			<a href="{{ route('items.create') }}" class="c-header__link">出品</a>
			<a href="{{ url('/logout') }}"
				onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
				class="c-header__link">ログアウト</a>
			<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">
				@csrf
			</form>
			@else
			<a href="{{ route('login') }}" class="c-header__link">ログイン</a>
			<a href="{{ route('register') }}" class="c-header__button">会員登録</a>
			@endauth
		</nav>
	</div>
</header>
