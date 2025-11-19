<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
		return [
			'payment' => ['required', 'in:card,konbini'],
		];
    }

	public function messages(): array
	{
		return [
			'payment.required' => '支払い方法を選択してください',
			'payment.in'       => '支払い方法を選択してください',
		];
	}
}
