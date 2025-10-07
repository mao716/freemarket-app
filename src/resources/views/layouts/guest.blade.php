<!doctype html>
<html lang="ja">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>@yield('title', 'Freemarket')</title>
	<link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>

<body>
	<x-site-header type="simple" />
	<main class="layout-narrow">@yield('content')</main>
</body>

</html>
