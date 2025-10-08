<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
			'name' => ['required', 'string', 'max:255'],
			'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
			'password' => ['required', 'string', 'min:8', 'confirmed'],
			'password_confirmation' => ['required', 'string', 'min:8'],
        ];
    }

	public function messages(): array
	{
		return [
			// 未入力
			'name.required' => 'お名前を入力してください',
			'email.required' => 'メールアドレスを入力してください',
			'email.email'    => 'メールアドレスはメール形式で入力してください',
			'password.required' => 'パスワードを入力してください',
			// ルール違反
			'password.min'       => 'パスワードは8文字以上で入力してください',
			'password.confirmed' => 'パスワードと一致しません',
			// 追加（確認用も必須にするなら）
			'password_confirmation.required' => 'パスワードを入力してください',
			'password_confirmation.min'      => 'パスワードは8文字以上で入力してください',
			// 重複
			'email.unique' => 'このメールアドレスはすでに登録されています',
		];
	}
}
