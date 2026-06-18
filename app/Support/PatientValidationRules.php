<?php

namespace App\Support;

use App\Models\Patient;
use Illuminate\Validation\Rule;

class PatientValidationRules
{
    public function rules(?Patient $patient = null): array
    {
        $uniqueMedicalNumber = Rule::unique('patients', 'no_rm');

        if ($patient) {
            $uniqueMedicalNumber->ignore($patient);
        }

        return [
            'nama' => ['required', 'string', 'max:255'],
            'no_rm' => ['required', 'string', 'max:255', $uniqueMedicalNumber],
            'kategori_pasien' => ['required', Rule::in(['dewasa', 'anak'])],
            'jenis_kelamin' => ['required', Rule::in(['L', 'P'])],
            'tanggal_lahir' => ['required', 'date', 'before_or_equal:today'],
            'umur' => ['required', 'integer', 'min:0', 'max:150'],
            'pekerjaan' => ['nullable', 'string', 'max:255'],
            'alamat' => ['nullable', 'string'],
        ];
    }
}
