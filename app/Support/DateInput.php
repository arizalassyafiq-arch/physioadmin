<?php

namespace App\Support;

use Carbon\CarbonImmutable;
use DateTimeInterface;
use Throwable;

class DateInput
{
    private const DISPLAY_FORMAT = 'm/d/Y';
    private const STORAGE_FORMAT = 'Y-m-d';

    public static function display(mixed $value): string
    {
        if ($value instanceof DateTimeInterface) {
            return $value->format(self::DISPLAY_FORMAT);
        }

        if (! is_string($value) || trim($value) === '') {
            return '';
        }

        $value = trim($value);

        foreach ([self::STORAGE_FORMAT, 'm/d/Y', 'm-d-Y', 'd/m/Y', 'd-m-Y'] as $format) {
            $date = self::parseStrict($value, $format);

            if ($date) {
                return $date->format(self::DISPLAY_FORMAT);
            }
        }

        return $value;
    }

    public static function normalize(mixed $value): mixed
    {
        if ($value instanceof DateTimeInterface) {
            return $value->format(self::STORAGE_FORMAT);
        }

        if (! is_string($value) || trim($value) === '') {
            return $value;
        }

        $value = trim($value);

        foreach (['m/d/Y', 'm-d-Y', self::STORAGE_FORMAT, 'd/m/Y', 'd-m-Y'] as $format) {
            $date = self::parseStrict($value, $format);

            if ($date) {
                return $date->format(self::STORAGE_FORMAT);
            }
        }

        return $value;
    }

    private static function parseStrict(string $value, string $format): ?CarbonImmutable
    {
        try {
            $date = CarbonImmutable::createFromFormat($format, $value);
        } catch (Throwable) {
            return null;
        }

        if (! $date || $date->format($format) !== $value) {
            return null;
        }

        return $date->startOfDay();
    }
}
