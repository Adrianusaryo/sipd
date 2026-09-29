<?php

namespace App\Helpers;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PaginationHelper
{
    public static function format(LengthAwarePaginator $paginator): array
    {
        return [
            'items' => array_map(fn ($item) => $item->toArray(), $paginator->items()),
            'limit' => $paginator->total(),
            'page' => $paginator->perPage(),
            'currentPage' => $paginator->currentPage(),
            'lastPage' => $paginator->lastPage(),
        ];
    }
}
