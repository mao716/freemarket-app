<!doctype html>
<html lang="ja">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>@yield('title', 'Freemarket')</title>
	<link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
	<link rel="stylesheet" href="{{ asset('css/common.css') }}">
</head>

<body>
	<x-site-header type="global" />
	<main class="layout-main">@yield('content')</main>
</body>

</html>
