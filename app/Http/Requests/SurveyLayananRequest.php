<?php

namespace App\Http\Requests;

use App\Enums\PendidikanEnum;
use App\Enums\JenisKelaminEnum;
use App\Enums\JenisPekerjaanEnum;
use App\Helpers\ResponseFormatter;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class SurveyLayananRequest extends FormRequest
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
            'nama' => 'required',
            'nomor_telepon' => 'required',
            'email' => 'required|email',
            'pendidikan' => 'required|in:' . implode(',', PendidikanEnum::values()),
            'pekerjaan' => 'required|in:' . implode(',', JenisPekerjaanEnum::values()),
            'jenis_kelamin' => 'required|in: ' . implode(',', JenisKelaminEnum::values()),
            'jenis_layanan_id' => 'required',
            'jawaban' => 'required|array',
            'jawaban.*.pertanyaan_id' => 'required|exists:kuesioner_pertanyaans,id',
            'jawaban.*.opsi_id' => 'required|exists:kuesioner_opsis,id',
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
