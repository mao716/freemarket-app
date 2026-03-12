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
}
