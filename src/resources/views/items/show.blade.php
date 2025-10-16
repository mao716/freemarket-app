@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/item-detail.css') }}">
@endpush

@section('content')
<main class="layout-main">
	<article class="page-section layout-narrow item-detail"><!-- item-detail はこのページ用のラッパークラス（包む箱） -->
		<section class="item-grid">
			{{-- 左：画像 --}}
			<figure class="item-media">
				<img class="item-image" src="{{ $item->image_url }}" alt="{{ $item->name }}">
			</figure>

			{{-- 右：すべての商品情報ブロック --}}
			<div class="item-summary">

				{{-- タイトル・ブランド・価格 --}}
				<h1 class="page-title item-title">{{ $item->name }}</h1>
				<p class="item-brand">{{ $item->brand ?: '—' }}</p>
				<p class="item-price">¥{{ number_format($item->price) }}</p>

				{{-- メタ行（いいね／コメント件数） --}}
				<div class="item-meta">
					@auth
					@if($isLiked)
					<form method="POST" action="{{ route('like.remove', $item) }}" class="inline-form">
						@csrf @method('DELETE')
						<button type="submit" class="meta-item icon-button" aria-pressed="true">
							<img src="{{ asset('images/icons/icon-like-on.svg') }}" alt="いいね済み">
							<span class="count">{{ $likeCount }}</span>
						</button>
					</form>
					@else
					<form method="POST" action="{{ route('like.add', $item) }}" class="inline-form">
						@csrf
						<button type="submit" class="meta-item icon-button" aria-pressed="false">
							<img src="{{ asset('images/icons/icon-like-off.svg') }}" alt="いいね">
							<span class="count">{{ $likeCount }}</span>
						</button>
					</form>
					@endif
					@else
					<a href="{{ route('login') }}" class="meta-item icon-button" aria-pressed="false">
						<img src="{{ asset('images/icons/icon-like-off.svg') }}" alt="いいね（ログインへ）">
						<span class="count">{{ $likeCount }}</span>
					</a>
					@endauth

					{{-- コメントアイコン＋件数 --}}
					<a href="#comments" class="meta-item icon-button" aria-label="コメント一覧へ">
						<img src="{{ asset('images/icons/icon-comment.svg') }}" alt="" aria-hidden="true">
						<span class="count">{{ $commentCount }}</span>
					</a>
				</div>

				{{-- 購入ボタン --}}
				@php $purchaseUrl = route('purchase.confirm', ['item' => $item->id]); @endphp
				@if(!$isSold && !$isMine)
				<a href="{{ $purchaseUrl }}" class="button button-primary button-full">購入手続きへ</a>
				@else
				<button class="button button-full" disabled>購入できません（売却済み or 自分の商品）</button>
				@endif

				{{-- 商品情報 --}}
				<section class="item-info block">
					<h2 class="block-title">商品の情報</h2>
					<dl class="info-list">
						<div class="info-row">
							<dt>ブランド</dt>
							<dd>{{ $item->brand ?: '—' }}</dd>
						</div>
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
							<dt>状態</dt>
							<dd>{{ \App\Models\Item::CONDITION[$item->condition] ?? '—' }}</dd>
						</div>
					</dl>
				</section>

				{{-- 商品説明 --}}
				<section class="item-description block">
					<h2 class="block-title">商品説明</h2>
					<p class="desc">{{ $item->description }}</p>
				</section>

				{{-- コメント一覧 --}}
				<section id="comments" class="item-comments block">
					<h2 class="block-title">商品へのコメント</h2>
					<ul class="comment-list">
						@forelse($item->comments as $c)
						<li class="comment">
							<div class="comment-author">{{ $c->user->name }}</div>
							<p class="comment-body">{{ $c->body }}</p>
						</li>
						@empty
						<li class="comment comment--empty">まだコメントはありません。</li>
						@endforelse
					</ul>
				</section>

				{{-- コメント投稿フォーム（ログイン時のみ） --}}
				@auth
				<section class="item-comment-form block">
					<h3 class="block-title">コメントを送信する</h3>
					<form method="POST" action="{{ route('comments.store', $item) }}" class="form">
						@csrf
						<div class="form-row">
							<label class="form-label" for="body">コメント（必須・255文字まで）</label>
							<textarea id="body" name="body" class="input" rows="3" maxlength="255">{{ old('body') }}</textarea>
							@error('body') <p class="error">{{ $message }}</p> @enderror
						</div>
						<div class="form-row form-row--actions">
							<button class="button button-primary">コメントを送信</button>
						</div>
					</form>
				</section>
				@endauth
			</div>
		</section>
	</article>
</main>
@endsection
