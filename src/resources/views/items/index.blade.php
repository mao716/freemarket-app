@extends('layouts.base')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/items.css') }}">
@endpush

@section('title', '商品一覧')

@section('headerType', 'global')

@section('content')
<main class="layout-main">
	<section class="page-section items">

		<nav class="page-tabs" aria-label="表示切替">

			<a class="page-tab {{ $tab !== 'mylist' ? 'is-active' : '' }}"
				href="{{ route('items.index', array_filter(['keyword' => $keyword ?: null])) }}">
				おすすめ
			</a>

			<a class="page-tab {{ $tab === 'mylist' ? 'is-active' : '' }}"
				href="{{ route('items.index', array_filter(['tab' => 'mylist', 'keyword' => $keyword ?: null])) }}">
				マイリスト
			</a>

		</nav>

		@if ($items->isEmpty())
		<p class="page-note">該当する商品がありません。</p>
		@else

		<ul class="item-grid" aria-live="polite">
			@foreach($items as $item)
			<li class="item-card">
				<a class="item-card-link" href="{{ route('items.detail', $item) }}">

					<div class="item-thumb">
						<img src="{{ $item->image_url }}" alt="{{ $item->name }}">
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
		@endif
	</section>
</main>
@endsection
