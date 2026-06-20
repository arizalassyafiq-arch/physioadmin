<?php

namespace App\Http\Requests;

use App\Support\DateInput;
use App\Support\PatientValidationRules;
use Illuminate\Foundation\Http\FormRequest;

class PatientRequest extends FormRequest
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
    public function rules(PatientValidationRules $rules): array
    {
        return $rules->rules($this->route('patient'));
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'tanggal_lahir' => DateInput::normalize($this->input('tanggal_lahir')),
        ]);
    }

    public function attributes(): array
    {
        return [
            'nama' => 'nama pasien',
            'no_rm' => 'nomor rekam medis',
            'kategori_pasien' => 'kategori pasien',
            'jenis_kelamin' => 'jenis kelamin',
            'tanggal_lahir' => 'tanggal lahir',
            'pekerjaan' => 'pekerjaan',
            'alamat' => 'alamat',
        ];
    }
}
