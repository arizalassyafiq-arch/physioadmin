<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PatientControlSchedule extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'patient_id',
        'control_number',
        'scheduled_date',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_date' => 'date',
        ];
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function statusLabel(): string
    {
        if ($this->status === 'completed') {
            return 'Selesai';
        }

        if ($this->scheduled_date?->isToday()) {
            return 'Hari ini';
        }

        if ($this->scheduled_date?->isPast()) {
            return 'Terlambat';
        }

        return 'Terjadwal';
    }

    public function statusTone(): string
    {
        return match ($this->statusLabel()) {
            'Selesai' => 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200',
            'Hari ini' => 'bg-blue-50 text-blue-700 ring-1 ring-blue-200',
            'Terlambat' => 'bg-red-50 text-red-700 ring-1 ring-red-200',
            default => 'bg-amber-50 text-amber-700 ring-1 ring-amber-200',
        };
    }
}
