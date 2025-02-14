<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCreationRequest extends FormRequest
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
            'area_id' => ['required', 'exists:areas,id'],
            'genre_id' => ['required', 'exists:genres,id'],
            'description' => ['required', 'string'],
            'image' => ['required', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => '店舗名は必須です。',
            'name.string' => '店舗名は文字列で入力してください。',
            'name.max' => '店舗名は255文字以内で入力してください。',

            'area_id.required' => 'エリアは必須です。',
            'area_id.exists' => '選択されたエリアは有効ではありません。',

            'genre_id.required' => 'ジャンルは必須です。',
            'genre_id.exists' => '選択されたジャンルは有効ではありません。',

            'description.required' => '店舗説明は必須です。',
            'description.string' => '店舗説明は文字列で入力してください。',

            'image.required' => '画像ファイルは必須です。',
            'image.image' => '指定されたファイルが画像ではありません。',
            'image.mimes' => '指定された形式（jpeg、png、jpg）の画像を選択してください。',
            'image.max' => '画像ファイルは2MB以下にしてください。',
        ];
    }
}
