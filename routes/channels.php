<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Channel GLOBAL untuk Superadmin
Broadcast::channel('superadmin-notify', function ($user) {
    // Return true jika user boleh dengar channel ini
    return $user->role === 'superadmin';
});

// Channel Per-Cabang
Broadcast::channel('branch-notify.{branchId}', function ($user, $branchId) {
    if ($user->role === 'superadmin') return true;
    if ($user->role === 'audit') {
        return in_array($branchId, $user->access_cabang_ids ?? []);
    }
    return false;
});