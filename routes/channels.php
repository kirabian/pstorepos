<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\User;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('superadmin-notify', function ($user) {
    // Explicitly cast to boolean
    return (bool) ($user->role === 'superadmin');
});

Broadcast::channel('branch-notify.{branchId}', function ($user, $branchId) {
    if ($user->role === 'superadmin') return true;
    if ($user->role === 'audit') {
        return in_array($branchId, $user->access_cabang_ids ?? []);
    }
    return false;
});