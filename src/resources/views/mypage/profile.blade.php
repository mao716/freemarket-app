@extends('layouts.base')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/items.css') }}">
<link rel="stylesheet" href="{{ asset('css/mypage.css') }}">
@endpush

@section('title', 'マイページ')

@section('content')
<main class="layout-main">
	<div class="mypage">

		<h1 class="visually-hidden">
			@if ($tab === 'buy')
			購入した商品一覧
			@elseif ($tab === 'trade')
			取引中の商品一覧
			@else
			出品した商品一覧
			@endif
		</h1>

		<section class="page-section">
			<div class="comment-head profile-head">
				<img class="avatar"
					src="{{ $user->avatar_url }}"
					alt="{{ $user->name }}のアイコン">
				<div class="comment-author">{{ $user->name }}</div>
				<a class="button button-outline profile-edit" href="{{ route('mypage.edit') }}">
					プロフィールを編集
				</a>
			</div>

			<nav class="page-tabs" aria-label="表示切替（タブ）">
				<a class="page-tab {{ $tab === 'sell' ? 'is-active' : '' }}"
					href="{{ route('mypage.profile', ['page' => 'sell']) }}">
					出品した商品
				</a>

				<a class="page-tab {{ $tab === 'buy' ? 'is-active' : '' }}"
					href="{{ route('mypage.profile', ['page' => 'buy']) }}">
					購入した商品
				</a>

				<a class="page-tab page-tab--trade {{ $tab === 'trade' ? 'is-active' : '' }}"
					href="{{ route('mypage.profile', ['page' => 'trade']) }}">
					取引中の商品
					@if ($tradeUnreadCount > 0)
					<span class="page-tab__badge">
						<span class="page-tab__badge-number">{{ $tradeUnreadCount }}</span>
					</span>
					@endif
				</a>
			</nav>
		</section>

		<section class="page-section items">
			@if ($tab === 'buy')
			@if ($purchasedItems->isEmpty())
			<p class="page-note">購入した商品はまだありません。</p>
			@else
			<ul class="item-grid" aria-live="polite">
				@foreach ($purchasedItems as $item)
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
			@elseif ($tab === 'trade')
			@if ($tradingItems->isEmpty())
			<p class="page-note">取引中の商品はまだありません。</p>
			@else
			<ul class="item-grid" aria-live="polite">
				@foreach ($tradingItems as $trade)
				@php
				$item = $trade->order->item;
				$unreadCount = $user->id === $trade->buyer_id
				? $trade->buyer_unread_count
				: $trade->seller_unread_count;
				@endphp
				<li class="item-card">
					<a class="item-card-link" href="{{ route('trades.show', ['trade' => $trade->id]) }}">
						<div class="item-thumb">
							<img src="{{ $item->image_url }}" alt="{{ $item->name }}">
							@if ($unreadCount > 0)
							<span class="item-badge item-badge--unread">{{ $unreadCount }}</span>
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

			@else
			@if ($sellingItems->isEmpty())
			<p class="page-note">出品した商品はまだありません。</p>
			@else
			<ul class="item-grid" aria-live="polite">
				@foreach ($sellingItems as $item)
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
			@endif
		</section>
	</div>
</main>
@endsection
