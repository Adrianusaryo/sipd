<?php

namespace App\Services;

use App\Models\ApprovalLog;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ApprovalLogsService
{
    protected string $cacheKey = 'approval_logs';

    public function clearLogCache(): void
    {
        DB::table('cache')->where('key', 'like', config('cache.prefix').$this->cacheKey.'%')->delete();
    }

    public function getLogsByUser(User $user, int $page = 1, int $perPage = 10): array
    {
        $role = $user->hasRole('applicant', 'api') ? "applicant_{$user->id}" : 'verificator_all';

        $dynamicKey = "{$this->cacheKey}_{$role}_page_{$page}_per_{$perPage}";

        return Cache::remember($dynamicKey, now()->addHours(1), function () use ($user, $perPage) {
            $query = ApprovalLog::with(['document:id,title,number_registration', 'actor:id,name,email'])->latest();

            if ($user->hasRole('applicant', 'api')) {
                $query->whereHas('document', function ($q) use ($user) {
                    $q->where('applicant_id', $user->id);
                });
            }

            $paginator = $query->paginate($perPage);

            return [
                'items' => array_map(fn ($item) => $item->toArray(), $paginator->items()),
                'total' => $paginator->total(),
                'perPage' => $paginator->perPage(),
                'currentPage' => $paginator->currentPage(),
                'lastPage' => $paginator->lastPage(),
            ];
        });
    }
}
