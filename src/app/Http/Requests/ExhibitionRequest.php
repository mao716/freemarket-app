<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Item;

class ExhibitionRequest extends FormRequest
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
			'name'        => ['required', 'string', 'max:255'],
			'description' => ['required', 'string', 'max:255'],
			'image'       => ['required', 'image', 'mimes:jpeg,png'],
			'categories'  => ['required', 'array', 'min:1'],
			'categories.*' => ['integer', 'exists:categories,id'],
			'condition'   => [
				'required',
				'integer',
				Rule::in(array_keys(Item::CONDITION)),
			],
			'price'       => ['required', 'integer', 'min:0'],
			'brand'       => ['nullable', 'string', 'max:255'],
		];
	}

	public function messages(): array
	{
		return [
			'name.required'        => '商品名を入力してください',
			'description.required' => '商品の説明を入力してください',
			'description.max'      => '商品の説明は255文字以内で入力してください',
			'image.required'       => '商品画像を選択してください',
			'image.image'          => '商品画像は画像ファイルを選択してください',
			'image.mimes'          => '商品画像はjpegまたはpng形式で選択してください',
			'categories.required'  => 'カテゴリーを選択してください',
			'condition.required'   => '商品の状態を選択してください',
			'price.required'       => '販売価格を入力してください',
			'price.integer'        => '販売価格は数値で入力してください',
			'price.min'            => '販売価格は0円以上で入力してください',
		];
	}
}
