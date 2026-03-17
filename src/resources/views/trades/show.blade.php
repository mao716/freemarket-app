@extends('layouts.base')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/trade.css') }}">
@endpush

@section('title', '取引画面')
@section('headerType', 'simple')

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

			<form
				method="POST"
				action="{{ route('trades.complete', ['trade' => $trade->id]) }}"
				class="trade-header__complete-form">

				@csrf

				@if ($userId === $trade->buyer_id && $trade->status === 0)
				<button
					class="button button-primary trade-header__complete-button"
					type="button"
					data-review-open>
					取引を完了する
				</button>
				@endif

			</form>
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

		@if (session('updated_message_id'))
		<input
			type="hidden"
			id="updatedMessageId"
			value="{{ session('updated_message_id') }}">
		@endif

		@if (old('editing_message_id'))
		<input
			type="hidden"
			id="editingMessageId"
			value="{{ old('editing_message_id') }}">
		@endif

		<section class="trade-messages js-trade-messages">

			<section class="trade-messages js-trade-messages">
				@forelse ($trade->messages as $message)

				@php
				$isOwnMessage = $message->user_id === $userId;
				@endphp

				<article
					class="trade-message {{ $isOwnMessage ? 'trade-message--mine' : '' }}"
					data-message-id="{{ $message->id }}">
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
						<div
							class="trade-message__view"
							data-message-view
							@if (old('editing_message_id')==$message->id) hidden @endif>
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
							@if (old('editing_message_id') !=$message->id) hidden @endif>
							@csrf
							@method('PATCH')

							<textarea
								class="trade-message__edit-textarea"
								name="edit_body"
								rows="3">{{ old('editing_message_id') == $message->id ? old('edit_body') : $message->body }}</textarea>

							<input type="hidden" name="editing_message_id" value="{{ $message->id }}">

							@error('edit_body')
							<p class="error">{{ $message }}</p>
							@enderror

							<div class="trade-message__edit-image-area">
								@if (!empty($message->image_path))
								<div class="trade-message__edit-current-image-wrap" data-current-image-wrap>
									<p class="trade-message__edit-image-label">現在の画像</p>

									<img
										class="trade-message__edit-current-image"
										src="{{ asset('storage/' . $message->image_path) }}"
										alt="現在の取引メッセージ画像">

									<label class="trade-message__edit-remove">
										<input type="checkbox" name="remove_image">
										画像を削除
									</label>
								</div>
								@endif

								<div class="trade-message__edit-preview" data-edit-preview></div>
							</div>

							<div class="trade-message__edit-upload">
								<button
									class="button-outline trade-message__edit-image-button"
									type="button"
									data-edit-image-button>
									画像を変更
								</button>

								<input
									class="trade-message__edit-file"
									type="file"
									name="edit_image"
									accept="image/*"
									data-edit-file
									hidden>
							</div>

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

			<div
				class="trade-review-modal"
				data-review-modal
				@if (!($userId===$trade->seller_id && $trade->status === 1)) hidden @endif>

				<div class="trade-review-modal__overlay" data-review-close></div>

				<div class="trade-review-modal__content">
					<form
						class="trade-review-modal__form"
						method="POST"
						action="{{ route('trades.complete', ['trade' => $trade->id]) }}">
						@csrf

						<div class="trade-review-modal__header">
							<p class="trade-review-modal__title">取引が完了しました。</p>
						</div>

						<div class="trade-review-modal__body">
							<p class="trade-review-modal__text">今回の取引相手はどうでしたか？</p>

							@error('rating')
							<p class="error">{{ $message }}</p>
							@enderror

							<div class="trade-review-stars">
								@for ($i = 1; $i <= 5; $i++)
									<label class="trade-review-stars__label">
									<input
										class="trade-review-stars__input"
										type="radio"
										name="rating"
										value="{{ $i }}"
										{{ old('rating') == $i ? 'checked' : '' }}>
									<span class="trade-review-stars__star">★</span>
									</label>
									@endfor
							</div>
						</div>

						<div class="trade-review-modal__footer">
							<button
								class="button button-primary trade-review-modal__submit"
								type="submit">
								送信する
							</button>
						</div>
					</form>
				</div>
			</div>
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
		const editingMessageId = document.getElementById('editingMessageId');
		const updatedMessageId = document.getElementById('updatedMessageId');

		if (messageArea) {
			const targetMessageId = editingMessageId ?
				editingMessageId.value :
				updatedMessageId ?
				updatedMessageId.value :
				null;

			if (targetMessageId) {
				const targetMessage = document.querySelector(
					'[data-message-id="' + targetMessageId + '"]'
				);

				if (targetMessage) {
					targetMessage.scrollIntoView({
						behavior: 'auto',
						block: 'center',
					});
				}
			} else {
				messageArea.scrollTop = messageArea.scrollHeight;
			}
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
