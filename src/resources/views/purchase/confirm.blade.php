@extends('layouts.base')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/purchase.css') }}">
@endpush

@section('title', '購入手続き')

@section('content')
<main class="layout-main">
	<section class="page-section">
		<div class="purchase-grid">
			<div class="purchase-left">
				<div class="product-card">
					<img
						class="product-thumb"
						src="{{ Storage::url($item->image_path) }}"
						alt="{{ $item->name }}">
					<div class="product-meta">
						<div class="product-name">{{ $item->name }}</div>
						<div class="product-price">¥ {{ number_format($item->price) }}</div>
					</div>
				</div>
				<hr class="divider">

				<form
					method="POST"
					action="{{ route('purchase.store', $item) }}"
					class="form"
					id="purchaseForm">
					@csrf
					<div class="form-row">
						<label class="form-label">支払い方法</label>
						<div class="select-row">
							<select id="payment" name="payment" class="input input--select">
								<option value="">選択してください</option>
								<option value="konbini" {{ old('payment', 'konbini') === 'konbini' ? 'selected' : '' }}>
									コンビニ払い
								</option>
								<option value="card" {{ old('payment') === 'card' ? 'selected' : '' }}>
									カード払い
								</option>
							</select>
						</div>
						@error('payment')
						<p class="error">{{ $message }}</p>
						@enderror
					</div>

					<hr class="divider">

					<div class="form-row form-row--purchase">
						<div class="address-row">
							<div class="address-col">
								<label class="form-label">配送先</label>
								<div>〒 {{ $address['postal_code'] ?? '' }}</div>
								<div>{{ $address['address'] ?? '' }}</div>
								@if (!empty($address['building']))
								<div>{{ $address['building'] }}</div>
								@endif
							</div>
							<div class="address-action">
								<a class="link-change" href="{{ route('address.show', $item) }}">変更する</a>
							</div>
						</div>
					</div>

					<hr class="divider divider--last">
				</form>
			</div>

			<aside class="purchase-right">
				<div class="summary-card">
					<div class="summary-row">
						<div class="summary-label">商品代金</div>
						<div class="summary-value">¥ {{ number_format($item->price) }}</div>
					</div>
					<div class="summary-row">
						<div class="summary-label">支払い方法</div>
						<div class="summary-value" id="paymentSummary">
							{{ old('payment', 'konbini') === 'card' ? 'カード払い' : 'コンビニ払い' }}
						</div>
					</div>
				</div>

				<button
					type="submit"
					form="purchaseForm"
					class="button button-primary button-full purchase-button">
					購入する
				</button>
			</aside>
		</div>
	</section>
</main>
@endsection

@push('scripts')
<script src="{{ asset('js/purchase.js') }}" defer></script>
@endpush
