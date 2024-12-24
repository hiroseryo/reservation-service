<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class ReservationRequest extends FormRequest
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
            'date' => ['required', 'date', 'after_or_equal:tomorrow'],
            'time' => ['required', 'date_format:H:i'],
            'num_of_users' => ['required', 'integer', 'min:1'],
        ];
    }

    public function messages()
    {
        return [
            'date.required' => '予約日を入力してください',
            'date.date' => '予約日を正しく入力してください',
            'date.after_or_equal' => '予約日は明日以降の日付を入力してください',

            'time.required' => '予約時間を入力してください',
            'time.date_format' => '時間は「時:分」の形式で入力してください',

            'num_of_users.required' => '予約人数を入力してください',
            'num_of_users.integer' => '予約人数を数字で入力してください',
            'num_of_users.min' => '予約人数は1人以上で入力してください',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $response = redirect()
            ->back()
            ->withErrors($validator)
            ->withInput()
            ->with('validation_error', '更新は行われませんでした。入力内容を確認して下さい。');

        throw new ValidationException($validator, $response);
    }
}
