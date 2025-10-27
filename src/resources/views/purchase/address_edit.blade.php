{{-- resources/views/purchase/address_edit.blade.php --}}
@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/purchase.css') }}">
@endpush

@section('content')
<main class="layout-narrow">
	<section class="page-section">
		<h1 class="page-title">住所の変更</h1>

		<form method="POST" action="{{ route('address.save', $item) }}" class="form">
			@csrf

			<div class="form-row">
				<label class="form-label" for="postal_code">郵便番号</label>
				<input id="postal_code" name="postal_code" type="text" class="input"
					value="{{ old('postal_code', $seed['postal_code'] ?? '') }}" placeholder="123-4567">
				@error('postal_code') <p class="error">{{ $message }}</p> @enderror
			</div>

			<div class="form-row">
				<label class="form-label" for="address">住所</label>
				<input id="address" name="address" type="text" class="input"
					value="{{ old('address', $seed['address'] ?? '') }}" placeholder="東京都〇〇区〇〇1-2-3">
				@error('address') <p class="error">{{ $message }}</p> @enderror
			</div>

			<div class="form-row">
				<label class="form-label" for="building">建物名</label>
				<input id="building" name="building" type="text" class="input"
					value="{{ old('building', $seed['building'] ?? '') }}" placeholder="マンション名 101号室">
				@error('building') <p class="error">{{ $message }}</p> @enderror
			</div>

			<div class="form-row form-row--actions">
				<button type="submit" class="button button-primary button-full">更新する</button>
			</div>
		</form>
	</section>
</main>
@endsection
