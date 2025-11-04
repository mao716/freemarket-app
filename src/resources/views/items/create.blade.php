@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/items-sell.css') }}">
@endpush

@section('title', '商品を出品')

@section('content')
<section class="page-section sell">
	<h1 class="page-title">商品を出品</h1>

	<form class="form sell-form" method="POST" action="{{ route('sell.post') }}" enctype="multipart/form-data">
		@csrf

		{{-- 商品画像 --}}
		<div class="form-row">
			<label class="form-label" for="image">商品画像</label>
			<div class="uploader">
				<input id="image" name="image" type="file" accept=".jpg,.jpeg,.png" class="uploader-input">
				<div class="uploader-drop">
					<div class="uploader-icon">📷</div>
					<p class="uploader-text">画像をドラッグ＆ドロップ、またはクリックして選択</p>
					<p class="uploader-note">JPEG/PNG形式・最大◯MB</p>
				</div>
				<div class="uploader-preview" id="uploader-preview"></div>
			</div>
			@error('image')
			<p class="error">{{ $message }}</p>
			@enderror
		</div>

		{{-- カテゴリ --}}
		<div class="form-row">
			<label class="form-label" for="categories">カテゴリ</label>
			<select id="categories" name="categories[]" multiple class="input">
				@foreach($categories as $category)
				<option value="{{ $category->id }}"
					{{ collect(old('categories'))->contains($category->id) ? 'selected' : '' }}>
					{{ $category->name }}
				</option>
				@endforeach
			</select>
			@error('categories')
			<p class="error">{{ $message }}</p>
			@enderror
		</div>

		{{-- 商品の状態 --}}
		<div class="form-row">
			<label class="form-label" for="condition">商品の状態</label>
			<select id="condition" name="condition" class="input select">
				<option value="">選択してください</option>
				<option value="1" {{ old('condition') == 1 ? 'selected' : '' }}>新品・未使用</option>
				<option value="2" {{ old('condition') == 2 ? 'selected' : '' }}>目立った傷や汚れ
