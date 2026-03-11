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
			'body' => ['nullable', 'string', 'max:1000'],
			'image' => ['nullable', 'image', 'max:2048'],
		];
	}
}
