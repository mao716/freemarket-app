<!doctype html>
<html lang="ja">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>@yield('title', 'Freemarket')</title>
	{{-- 共通CSSの読み込み（asset＝public配下のURLを生成） --}}
	<link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>

<body>
	<x-site-header /> {{-- 共通ヘッダー（コンポーネント） --}}

	<main class="l-main">
		@yield('content')
	</main>
</body>

</html>
