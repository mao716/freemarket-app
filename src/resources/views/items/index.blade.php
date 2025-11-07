@extends('layouts.base')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/items.css') }}">
@endpush

@section('title', '商品一覧')

@section('headerType', 'global')

@section('content')
<main class="layout-main">
	<section class="page-section items">

		{{-- タブ（おすすめ / マイリスト） --}}
		<nav class="page-tabs" aria-label="表示切替">

			{{-- おすすめタブ --}}
			<a class="page-tab {{ $tab !== 'mylist' ? 'is-active' : '' }}"
				href="{{ route('items.index', array_filter(['keyword' => $keyword ?: null])) }}">
				おすすめ
			</a>

			{{-- マイリストタブ --}}
			@auth
			{{-- ログイン中ならマイリスト画面へ --}}
			<a class="page-tab {{ $tab === 'mylist' ? 'is-active' : '' }}"
				href="{{ route('items.index', array_filter(['tab' => 'mylist', 'keyword' => $keyword ?: null])) }}">
				マイリスト
			</a>
			@else
			{{-- ログインしてなければログイン画面へ --}}
			<a class="page-tab" href="{{ route('login') }}">
				マイリスト
			</a>
			@endauth

		</nav>

		{{-- 検索結果がない場合 --}}
		@if ($items->isEmpty())
		<p class="page-note">該当する商品がありません。</p>
		@else
		{{-- 商品カード一覧 --}}
		<ul class="item-grid" aria-live="polite">
			@foreach($items as $item)
			<li class="item-card">
				<a class="item-card-link" href="{{ route('items.detail', $item) }}">

					<div class="item-thumb">
						<img src="{{ Storage::url($item->image_path) }}" alt="{{ $item->name }}">
						@if ($item->order)
						<span class="item-badge">SOLD</span>
						@endif
					</div>


					<div class="item-body">
						<h2 class="item-name">{{ $item->name }}</h2>
					</div>
				</a>
			</li>
			@endforeach
		</ul>

		{{-- ページネーション（ページ分割リンク） --}}
		{{ $items->links() }}
		@endif
	</section>
</main>
@endsection
