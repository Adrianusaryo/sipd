<?php

use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('verificators', function (User $user) {
    return $user->hasRole('verificator');
});

Broadcast::channel('applicant.{id}', function (User $user, int $id) {
    return (int) $user->id === (int) $id;
});

// Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
//     return (int) $user->id === (int) $id;
// });
