@extends('layouts.base')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/item-detail.css') }}">
@endpush

@section('title', '商品詳細画面')

@section('headerType', 'global')

@section('content')
<main class="layout-main">
	<article class="page-section item-detail">
		<section class="item-grid">

			<figure class="item-media">
				<img class="item-image" src="{{ Storage::url($item->image_path) }}" alt="{{ $item->name }}">
			</figure>

			<div class="item-summary">

				<h1 class="item-title">{{ $item->name }}</h1>
				<p class="item-brand">{{ $item->brand ?: '—' }}</p>
				<p class="item-price">¥{{ number_format($item->price) }}</p>

				<div class="item-meta">
					@auth
					@if($isLiked)
					<form method="POST" action="{{ route('like.remove', $item) }}" class="inline-form">
						@csrf @method('DELETE')
						<button type="submit" class="meta-item icon-button" aria-pressed="true">
							<div class="icon-wrap">
								<img src="{{ asset('images/icons/icon-like-on.svg') }}" alt="いいね済み">
								<span class="count">{{ $likeCount }}</span>
							</div>
						</button>
					</form>
					@else
					<form method="POST" action="{{ route('like.add', $item) }}" class="inline-form">
						@csrf
						<button type="submit" class="meta-item icon-button" aria-pressed="false">
							<div class="icon-wrap">
								<img src="{{ asset('images/icons/icon-like-off.svg') }}" alt="いいね">
								<span class="count">{{ $likeCount }}</span>
							</div>
						</button>
					</form>
					@endif
					@else
					<a href="{{ route('login') }}" class="meta-item icon-button" aria-pressed="false">
						<div class="icon-wrap">
							<img src="{{ asset('images/icons/icon-like-off.svg') }}" alt="いいね（ログインへ）">
							<span class="count">{{ $likeCount }}</span>
						</div>
					</a>
					@endauth

					<a href="#comments" class="meta-item icon-button is-comment" aria-label="コメント一覧へ">
						<div class="icon-wrap">
							<img src="{{ asset('images/icons/icon-comment.svg') }}" alt="" aria-hidden="true">
							<span class="count">{{ $commentCount }}</span>
						</div>
					</a>
				</div>

				@if (!$isSold && !$isMine)
				@auth
				<a href="{{ route('purchase.confirm', $item) }}" class="button button-primary button-full">
					購入手続きへ
				</a>
				@else
				<a href="{{ route('login') }}" class="button button-primary button-full">
					購入手続きへ
				</a>
				@endauth
				@else
				<button class="button button-full" disabled>
					購入できません
				</button>
				@endif

				<section class="item-description block">
					<h2 class="block-title">商品説明</h2>
					<p class="desc">{{ $item->description }}</p>
				</section>

				<section class="item-info block">
					<h2 class="block-title">商品の情報</h2>
					<dl class="info-list">
						<div class="info-row">
							<dt>カテゴリー</dt>
							<dd>
								@forelse($item->categories as $cat)
								<span class="tag">{{ $cat->name }}</span>
								@empty
								—
								@endforelse
							</dd>
						</div>
						<div class="info-row">
							<dt>商品の状態</dt>
							<dd>{{ \App\Models\Item::CONDITION[$item->condition] ?? '—' }}</dd>
						</div>
					</dl>
				</section>

				<section id="comments" class="item-comments block">
					<h2 class="block-title">
						コメント<span class="comment-count">({{ $commentCount }})</span>
					</h2>
					<ul class="comment-list">
						@forelse($item->comments as $c)
						<li class="comment">
							<div class="comment-head">
								<img class="avatar" src="{{ $c->user->avatar_url }}" alt="{{ $c->user->name }}のアイコン">
								<div class="comment-author">{{ $c->user->name }}</div>
							</div>

							<div class="comment-body-wrap">
								<p class="comment-body">{{ $c->body }}</p>
							</div>
						</li>
						@empty
						<li class="comment comment--empty">まだコメントはありません。</li>
						@endforelse
					</ul>
				</section>

				@auth
				<section class="item-comment-form block">
					<h3 class="block-title">商品へのコメント</h3>
					<form method="POST" action="{{ route('comments.store', $item) }}" class="form">
						@csrf
						<div class="form-row">
							<textarea id="body" name="body" class="input" rows="7">{{ old('body') }}</textarea>
							@error('body') <p class="error">{{ $message }}</p> @enderror
						</div>
						<div class="form-row form-row--actions">
							<button class="button button-primary button-full">コメントを送信する</button>
						</div>
					</form>
				</section>
				@endauth

				@guest
				<section class="item-comment-form block">
					<h3 class="block-title">商品へのコメント</h3>
					<form action="{{ route('login') }}" method="GET" class="form">
						<div class="form-row">
							<textarea class="input" rows="7" maxlength="255"></textarea>
						</div>
						<div class="form-row form-row--actions">
							<button class="button button-primary button-full">コメントを送信する</button>
						</div>
					</form>
				</section>
				@endguest

			</div>
		</section>
	</article>
</main>
@endsection
