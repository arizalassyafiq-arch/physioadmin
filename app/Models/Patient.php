<?php

namespace App\Models;

use App\Support\AgeCalculator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Patient extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'nama',
        'no_rm',
        'kategori_pasien',
        'jenis_kelamin',
        'tanggal_lahir',
        'umur',
        'pekerjaan',
        'alamat',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_lahir' => 'date',
        ];
    }

    public function medicalRecords(): HasMany
    {
        return $this->hasMany(MedicalRecord::class);
    }

    public function latestMedicalRecord(): HasOne
    {
        return $this->hasOne(MedicalRecord::class)->latestOfMany();
    }

    public function categoryLabel(): string
    {
        return match ($this->kategori_pasien) {
            'anak' => 'Anak-anak',
            default => 'Dewasa',
        };
    }

    public function ageAt(string|\DateTimeInterface|null $date = null): int
    {
        return app(AgeCalculator::class)->yearsAt($this->tanggal_lahir, $date);
    }
}
