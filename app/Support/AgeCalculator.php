<?php

namespace App\Support;

use Carbon\CarbonImmutable;
use DateTimeInterface;

class AgeCalculator
{
    public function yearsAt(string|DateTimeInterface $birthDate, string|DateTimeInterface|null $date = null): int
    {
        $birth = $birthDate instanceof DateTimeInterface
            ? CarbonImmutable::instance($birthDate)
            : CarbonImmutable::parse($birthDate);

        $target = $date instanceof DateTimeInterface
            ? CarbonImmutable::instance($date)
            : CarbonImmutable::parse($date ?? now());

        return max(0, (int) $birth->diffInYears($target));
    }
}
