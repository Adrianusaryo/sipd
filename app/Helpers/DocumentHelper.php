<?php

namespace App\Helpers;

use App\Models\Document;
use Illuminate\Support\Str;

class DocumentHelper
{
    public static function registrationNumber(string $prefix = 'REG'): string
    {
        return sprintf('%s-%s-%s', $prefix, now()->format('Ymd'), strtoupper(Str::random(5)));
    }

    public static function getSummary(?int $userId = null) : array
    {
        $counts = Document::when($userId, function ($query, $userId) {
            $query->where('user_id',$userId);
        })
        ->selectRaw('status, count(*) as total')
        ->groupBy('status')
        ->pluck('total','status');

        return [
            'total' => $counts->sum(),
            'submitted' => $counts['submitted'] ?? 0,
            'revision' => $counts['revision'] ?? 0,
            'approved' => $counts['approved'] ?? 0,
            'rejected' => $counts['rejected'] ?? 0,
        ];
    }

}
