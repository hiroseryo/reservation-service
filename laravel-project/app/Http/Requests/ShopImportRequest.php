<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ShopImportRequest extends FormRequest
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
            'csv_file' => 'required|file|mimes:csv,txt',
        ];
    }

    public function messages(): array
    {
        return [
            'csv_file.required' => 'CSVまたはTXTファイルは必須です',
            'csv_file.file' => 'ファイルを選択してください',
            'csv_file.mimes' => 'CSVまたはTXTファイルのみアップロード可能です',

            'name.required'        => '店舗名は必須です',
            'name.max'             => '店舗名は50文字以内で入力してください',
            'area.required'        => '地域は必須です',
            'area.in'              => '地域は「東京都」「大阪府」「福岡県」から選択してください',
            'genre.required'       => 'ジャンルは必須です',
            'genre.in'             => 'ジャンルは「寿司」「焼肉」「イタリアン」「居酒屋」「ラーメン」から選択してください',
            'description.required' => '店舗概要は必須です',
            'description.max'      => '店舗概要は400文字以内で入力してください',
            'image_url.required'   => '画像URLは必須です',
            'image_url.url'        => '画像URLは正しいURL形式で入力してください',
            'image_url.regex'      => '画像URLはjpg、jpeg、png形式のみアップロード可能です',
        ];
    }
}
