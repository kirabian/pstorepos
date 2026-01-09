<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\User;

// Channel Pribadi User
Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// 1. Channel Superadmin (Hanya Superadmin yg bisa dengar)
Broadcast::channel('superadmin-notify', function (User $user) {
    return $user->role === 'superadmin';
});

// 2. Channel Cabang (Superadmin & Audit yg pegang cabang tsb yg bisa dengar)
Broadcast::channel('branch-notify.{branchId}', function (User $user, $branchId) {
    if ($user->role === 'superadmin') {
        return true;
    }
    
    if ($user->role === 'audit') {
        // Pastikan akses_cabang_ids ada di Model User
        return in_array($branchId, $user->access_cabang_ids ?? []);
    }

    return false;
});