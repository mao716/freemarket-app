@extends('layouts.base')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/trade.css') }}">
@endpush

@section('title', '取引画面')

@section('content')
<div class="trade-page">
	<h1 class="visually-hidden">取引画面</h1>

	<aside class="trade-sidebar">
		<p class="trade-sidebar__title">その他の取引</p>

		<div class="trade-sidebar__list">
			@foreach ($trades as $sidebarTrade)
			@php
			$sidebarItem = $sidebarTrade->order->item;
			$unreadCount = $userId === $sidebarTrade->buyer_id
			? $sidebarTrade->buyer_unread_count
			: $sidebarTrade->seller_unread_count;
			@endphp

			<a
				class="trade-sidebar__item {{ $sidebarTrade->id === $trade->id ? 'trade-sidebar__item--active' : '' }}"
				href="{{ route('trades.show', ['trade' => $sidebarTrade->id]) }}">
				<span class="trade-sidebar__item-name">{{ $sidebarItem->name }}</span>

				@if ($unreadCount > 0)
				<span class="trade-sidebar__badge">{{ $unreadCount }}</span>
				@endif
			</a>
			@endforeach
		</div>
	</aside>

	<main class="trade-main">
		<header class="trade-header">
			<div class="trade-header__user">
				@if ($userId === $trade->buyer_id)
				@php $partner = $trade->seller; @endphp
				@else
				@php $partner = $trade->buyer; @endphp
				@endif

				@if (!empty($partner->avatar_path))
				<img
					class="trade-header__avatar avatar-img"
					src="{{ asset('storage/' . $partner->avatar_path) }}"
					alt="{{ $partner->name }}">
				@else
				<div class="trade-header__avatar"></div>
				@endif
				<p class="trade-header__title">「{{ $partnerName }}」さんとの取引画面</p>
			</div>

			<button class="button button-primary trade-header__complete-button" type="button">
				取引を完了する
			</button>
		</header>

		<section class="trade-product">
			<div class="trade-product__image-wrap">
				<img
					class="trade-product__image"
					src="{{ asset('storage/' . $trade->order->item->image_path) }}"
					alt="{{ $trade->order->item->name }}">
			</div>

			<div class="trade-product__body">
				<h2 class="trade-product__name">{{ $trade->order->item->name }}</h2>
				<p class="trade-product__price">¥{{ number_format($trade->order->item->price) }}</p>
			</div>
		</section>

		<section class="trade-messages js-trade-messages">
			@forelse ($trade->messages as $message)
			@php
			$isOwnMessage = $message->user_id === $userId;
			@endphp

			<article class="trade-message {{ $isOwnMessage ? 'trade-message--mine' : '' }}">
				<div class="trade-message__meta">
					@if (!$isOwnMessage)
					@if (!empty($message->user->avatar_path))
					<img
						class="trade-message__avatar avatar-img"
						src="{{ asset('storage/' . $message->user->avatar_path) }}"
						alt="{{ $message->user->name }}">
					@else
					<div class="trade-message__avatar"></div>
					@endif
					<p class="trade-message__user">{{ $message->user->name }}</p>
					@else
					<p class="trade-message__user trade-message__user--mine">{{ $message->user->name }}</p>
					@if (!empty($message->user->avatar_path))
					<img
						class="trade-message__avatar avatar-img"
						src="{{ asset('storage/' . $message->user->avatar_path) }}"
						alt="{{ $message->user->name }}">
					@else
					<div class="trade-message__avatar"></div>
					@endif
					@endif
				</div>

				<div class="trade-message__bubble">
					<div class="trade-message__view" data-message-view>
						@if (!empty($message->body))
						<p class="trade-message__body">{{ $message->body }}</p>
						@endif

						@if (!empty($message->image_path))
						<img
							class="trade-message__image"
							src="{{ asset('storage/' . $message->image_path) }}"
							alt="取引メッセージ画像">
						@endif
					</div>

					@if ($isOwnMessage)
					<form
						class="trade-message__edit-form"
						method="POST"
						action="{{ route('trades.messages.update', ['trade' => $trade->id, 'message' => $message->id]) }}"
						enctype="multipart/form-data"
						data-message-edit-form
						hidden>
						@csrf
						@method('PATCH')

						<textarea
							class="trade-message__edit-textarea"
							name="body"
							rows="3">{{ old('body', $message->body) }}</textarea>

						<input
							type="file"
							name="image"
							accept="image/*">

						<div class="trade-message__edit-actions">
							<button
								class="trade-message__action"
								type="button"
								data-edit-cancel>
								キャンセル
							</button>
							<button
								class="trade-message__action trade-message__action--save"
								type="submit">
								保存
							</button>
						</div>
					</form>
					@endif
				</div>

				@if ($isOwnMessage)
				<div class="trade-message__actions">
					<button
						class="trade-message__action"
						type="button"
						data-edit-toggle>
						編集
					</button>

					<form
						method="POST"
						action="{{ route('trades.messages.destroy', ['trade' => $trade->id, 'message' => $message->id]) }}"
						onsubmit="return confirm('このメッセージを削除しますか？');">
						@csrf
						@method('DELETE')

						<button class="trade-message__action" type="submit">
							削除
						</button>
					</form>
				</div>
				@endif
			</article>
			@empty
			<p class="trade-messages__empty">まだメッセージはありません。</p>
			@endforelse
		</section>

		<section class="trade-form-area">
			<form
				class="trade-form"
				method="POST"
				action="{{ route('trades.messages.store', ['trade' => $trade->id]) }}"
				enctype="multipart/form-data">
				@csrf

				<div class="trade-form__row">
					<textarea
						class="trade-form__textarea"
						name="body"
						rows="1"
						placeholder="取引メッセージを記入してください">{{ old('body') }}</textarea>

					<div class="trade-form__actions">
						<button class="button-outline trade-form__image-button" type="button">
							画像を追加
						</button>

						<input
							type="file"
							name="image"
							accept="image/*"
							class="trade-form__file"
							hidden>

						<button class="trade-form__submit" type="submit" aria-label="メッセージを送信">
							<img src="{{ asset('images/icons/icon-send.svg') }}" alt="送信">
						</button>
					</div>
				</div>

				@error('body')
				<div class="error">{{ $message }}</div>
				@enderror

				<div class="trade-form__preview"></div>
			</form>
		</section>
	</main>
</div>
@endsection

@push('scripts')
<script>
	window.addEventListener('load', function() {
		const messageArea = document.querySelector('.js-trade-messages');
		const imageButton = document.querySelector('.trade-form__image-button');
		const fileInput = document.querySelector('.trade-form__file');
		const previewArea = document.querySelector('.trade-form__preview');

		if (messageArea) {
			messageArea.scrollTop = messageArea.scrollHeight;
		}

		if (imageButton && fileInput) {
			imageButton.addEventListener('click', function() {
				fileInput.click();
			});
		}

		if (fileInput && previewArea) {
			fileInput.addEventListener('change', function() {
				previewArea.innerHTML = '';

				const file = this.files[0];

				if (!file) {
					return;
				}

				const reader = new FileReader();

				reader.onload = function(e) {
					const img = document.createElement('img');
					img.src = e.target.result;
					img.classList.add('trade-form__preview-image');

					previewArea.appendChild(img);
				};

				reader.readAsDataURL(file);
			});
		}
	});
</script>
@endpush
