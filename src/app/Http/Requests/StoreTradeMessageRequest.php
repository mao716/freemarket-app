<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTradeMessageRequest extends FormRequest
{
	public function authorize(): bool
	{
		return true;
	}

	public function rules(): array
	{
		return [
			'body' => ['required', 'string', 'max:1000'],
			'image' => ['nullable', 'image', 'mimes:jpeg,png', 'max:2048'],
		];
	}

	public function messages(): array
	{
		return [
			'body.required' => '本文を入力してください',
			'body.max' => '本文は400文字以内で入力してください',
			'image.mimes' => '画像はjpegまたはpng形式でアップロードしてください',
			'image.max' => '画像は2MB以内でアップロードしてください',
		];
	}
}
