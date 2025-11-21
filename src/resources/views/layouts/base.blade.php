<!doctype html>
<html lang="ja">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>@yield('title', 'Freemarket')</title>
	<link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
	<link rel="stylesheet" href="{{ asset('css/common.css') }}">
	@stack('styles')
</head>

<body>
	@php($headerType = trim($__env->yieldContent('headerType', 'auto')))
	<x-site-header :type="$headerType === 'auto' ? (auth()->check() ? 'global' : 'simple') : $headerType" />

	@yield('content')

	@stack('scripts')
</body>

</html>
