<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\User;

// Channel Pribadi
Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Channel Superadmin
Broadcast::channel('superadmin-notify', function ($user) {
    // Pastikan return boolean explicit
    return $user->role === 'superadmin'; 
});

// Channel Cabang
Broadcast::channel('branch-notify.{branchId}', function ($user, $branchId) {
    if ($user->role === 'superadmin') return true;
    if ($user->role === 'audit') {
        return in_array($branchId, $user->access_cabang_ids ?? []);
    }
    return false;
});

// Channel Distributor
Broadcast::channel('distributor-notify.{distId}', function ($user, $distId) {
    return $user->role === 'superadmin';
});