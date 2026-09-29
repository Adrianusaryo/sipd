<?php

namespace App\Helpers;

use Illuminate\Support\Str;

class DocumentHelper
{
    public static function registrationNumber(string $prefix = 'REG'): string
    {
        return sprintf('%s-%s-%s', $prefix, now()->format('Ymd'), strtoupper(Str::random(5)));
    }
}
