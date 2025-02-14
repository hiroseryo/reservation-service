<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReviewRequest extends FormRequest
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
        $rules = [
            'rating' => ['required', 'min:1', 'max:5'],
            'comment' => ['required', 'max:400'],
        ];

        if (!$this->hasFile('image') && !$this->input('old_img')) {
            $rules['image'] = 'required|image|mimes:jpeg,png';
        } else {
            $rules['image'] = 'nullable|image|mimes:jpeg,png';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'rating.required' => '評価は必須です。',
            'rating.min' => '評価は1以上で入力してください。',
            'rating.max' => '評価は5以下で入力してください。',

            'comment.required' => 'コメントは必須です。',
            'comment.max' => 'コメントは400文字以内で入力してください。',

            'image.required' => '画像の選択が必須です。',
            'image.image' => '指定されたファイルが画像ではありません。',
            'image.mimes' => '指定された形式（jpeg、png）の画像を選択してください。',
        ];
    }
}
