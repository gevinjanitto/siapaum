<?php

namespace App\Http\Requests;

use App\Helpers\ResponseFormatter;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class StatistikRequest extends FormRequest
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
            'status' => 'nullable|in:permohonan_baru,verifikasi,verifikasi_ulang,revisi,selesai',
            'tahun' => 'nullable|integer',
            'bulan' => 'nullable|integer|between:1,12',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        if ($this->expectsJson()) {
            return ResponseFormatter::failedValidation($validator->errors());
        } else {
            return parent::failedValidation($validator);
        }
    }
}
