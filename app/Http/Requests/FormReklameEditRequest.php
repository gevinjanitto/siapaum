<?php

namespace App\Http\Requests;

use App\Models\FormJenisIzin;
use App\Enums\JenisReklameEnum;
use App\Helpers\ResponseFormatter;
use App\Enums\AreaPemasanganReklameEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class FormReklameEditRequest extends FormRequest
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
        $form_jenis_izin = FormJenisIzin::where('jenis_izin_id', 9)->get();
        $area_pemasangan = implode(',', array_values(AreaPemasanganReklameEnum::array()));
        $jenis_reklame = implode(',', array_values(JenisReklameEnum::array()));
        $rules = [
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ];
        foreach ($form_jenis_izin as $form) {
            $rules[$form->kode_isian] = 'required';
            if ($form->tipe == 'date') {
                $rules[$form->kode_isian] = 'required|date:Y-m-d';
            }
            if($form->kode_isian == 'AREA_PEMASANGAN') {
                $rules[$form->kode_isian] = 'required|in:' . $area_pemasangan;
            }
            if($form->kode_isian == 'JENIS_REKLAME') {
                $rules[$form->kode_isian] = 'required|in:' . $jenis_reklame;
            }
        }
        // get all value from App\Enums\AreaPemasanganReklameEnum
        unset($rules['NAMA_PERUSAHAAN']);
        unset($rules['HP/TELP']);
        unset($rules['ALAMAT']);
        unset($rules['LAMA_PEMASANGAN']);
        return $rules;
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
