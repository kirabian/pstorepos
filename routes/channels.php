<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\User;

// Channel untuk Superadmin
Broadcast::channel('superadmin-notify', function ($user) {
    return $user->role === 'superadmin';
});

// Channel untuk Audit (Per Cabang)
Broadcast::channel('branch-notify.{branchId}', function ($user, $branchId) {
    if ($user->role === 'superadmin') return true;
    if ($user->role === 'audit') {
        return in_array($branchId, $user->access_cabang_ids ?? []);
    }
    return false;
});