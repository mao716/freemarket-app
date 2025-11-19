@extends('layouts.base')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/items-sell.css') }}">
@endpush

@section('title', '商品の出品')

@section('content')
<main class="layout-narrow">
	<section class="page-section sell">
		<h1 class="page-title">商品の出品</h1>

		<form class="form sell-form" method="POST" action="{{ route('sell.perform') }}" enctype="multipart/form-data">
			@csrf
			<div class="form-row">
				<label class="form-label" for="image">商品画像</label>
				<div class="uploader">
					<input id="image" name="image" type="file" accept=".jpg,.jpeg,.png" class="uploader-input">
					<div class="uploader-drop">
						<div class="uploader-preview" id="uploader-preview"></div>
						<label for="image" class="button-outline uploader-button">画像を選択する</label>
					</div>
				</div>
			</div>

			<div class="form-block">
				<h2 class="sell-subtitle">商品の詳細</h2>
				<hr class="sell-hr">
			</div>

			<div class="form-row">
				<label class="form-label">カテゴリ</label>
				<div class="chip-list">
					@foreach ($categories as $category)
					@php $checked = collect(old('categories', []))->contains($category->id); @endphp
					<input
						type="checkbox"
						id="cat-{{ $category->id }}"
						name="categories[]"
						value="{{ $category->id }}"
						class="chip-input"
						{{ $checked ? 'checked' : '' }}>
					<label for="cat-{{ $category->id }}" class="chip">{{ $category->name }}</label>
					@endforeach
				</div>
				@error('categories') <p class="error">{{ $message }}</p> @enderror
			</div>

			<div class="form-row">
				<label class="form-label" for="condition">商品の状態</label>
				<select id="condition" name="condition" class="input select">
					<option value="">選択してください</option>
					<option value="1" {{ old('condition') == 1 ? 'selected' : '' }}>良好</option>
					<option value="2" {{ old('condition') == 2 ? 'selected' : '' }}>目立った傷や汚れなし</option>
					<option value="3" {{ old('condition') == 3 ? 'selected' : '' }}>やや傷や汚れあり</option>
					<option value="4" {{ old('condition') == 4 ? 'selected' : '' }}>状態が悪い</option>
				</select>
				@error('condition') <p class="error">{{ $message }}</p> @enderror
			</div>

			<div class="form-block">
				<h2 class="sell-subtitle">商品名と説明</h2>
				<hr class="sell-hr">
			</div>

			<div class="form-row">
				<label class="form-label" for="name">商品名</label>
				<input id="name" name="name" type="text" class="input" value="{{ old('name') }}">
				@error('name') <p class="error">{{ $message }}</p> @enderror
			</div>

			<div class="form-row">
				<label class="form-label" for="brand">ブランド名</label>
				<input id="brand" name="brand" type="text" class="input" value="{{ old('brand') }}">
				@error('brand') <p class="error">{{ $message }}</p> @enderror
			</div>

			<div class="form-row">
				<label class="form-label" for="description">商品の説明</label>
				<textarea id="description" name="description" rows="6" class="input textarea">
					{{ old('description') }}
				</textarea>
				@error('description') <p class="error">{{ $message }}</p> @enderror
			</div>

			<div class="form-row">
				<label class="form-label" for="price">販売価格</label>
				<div class="price-input">
					<input
						id="price"
						name="price"
						type="text"
						class="input price-field"
						value="{{ old('price') }}"
						>
				</div>
				@error('price') <p class="error">{{ $message }}</p> @enderror
			</div>

			<div class="form-row form-row--actions">
				<button class="button button-primary button-full" type="submit">出品する</button>
			</div>
		</form>
	</section>
</main>
@endsection

@push('scripts')
<script>
	document.addEventListener('DOMContentLoaded', () => {
		const input = document.getElementById('image');
		const preview = document.getElementById('uploader-preview');
		if (!input || !preview) return;

		input.addEventListener('change', (e) => {
			const file = e.target.files?.[0];
			preview.innerHTML = '';

			if (!file || !file.type.startsWith('image/')) return;

			const url = URL.createObjectURL(file);
			const img = document.createElement('img');
			img.src = url;
			img.onload = () => URL.revokeObjectURL(url);
			preview.appendChild(img);
		});
	});
</script>
@endpush
