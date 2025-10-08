<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
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
			'avatar'      => ['nullable', 'image', 'mimes:jpeg,png'],
			'name'        => ['required', 'string', 'max:20'],
			'postal_code' => ['required', 'regex:/^\d{3}-\d{4}$/'],
			'address'     => ['required', 'string', 'max:255'],
			'building'    => ['nullable', 'string', 'max:255'],
		];
    }

	public function messages(): array
	{
		return [
			'avatar.image' => 'プロフィール画像は画像ファイルを選択してください',
			'avatar.mimes' => 'プロフィール画像はjpegまたはpng形式で選択してください',
			'name.required' => 'ユーザー名を入力してください',
			'name.max'      => 'ユーザー名は20文字以内で入力してください',
			'postal_code.required' => '郵便番号を入力してください',
			'postal_code.regex'    => '郵便番号はハイフンありの8文字で入力してください',
			'address.required'     => '住所を入力してください',
		];
	}
}
