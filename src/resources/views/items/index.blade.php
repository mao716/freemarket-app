<form method="POST" action="{{ route('logout') }}">
	@csrf
	<button type="submit">ログアウト</button>
</form>
