<?php

namespace App\Support;

use App\Models\Patient;
use Illuminate\Validation\Rule;

class PatientValidationRules
{
    public function rules(?Patient $patient = null): array
    {
        return [
            'nama' => ['required', 'string', 'max:255'],
            'no_rm' => ['required', 'string', 'max:255'],
            'kategori_pasien' => ['required', Rule::in(['dewasa', 'anak'])],
            'jenis_kelamin' => ['required', Rule::in(['L', 'P'])],
            'tanggal_lahir' => ['required', 'date', 'before_or_equal:today'],
            'pekerjaan' => ['nullable', 'string', 'max:255'],
            'alamat' => ['nullable', 'string'],
        ];
    }
}
