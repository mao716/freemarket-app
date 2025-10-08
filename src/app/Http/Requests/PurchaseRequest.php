<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
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
