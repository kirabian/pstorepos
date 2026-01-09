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

// Superadmin Channel
Broadcast::channel('superadmin-notify', function ($user) {
    // Must return boolean
    return $user->role === 'superadmin';
});

// Branch Channel
Broadcast::channel('branch-notify.{branchId}', function ($user, $branchId) {
    if ($user->role === 'superadmin') {
        return true;
    }
    
    if ($user->role === 'audit') {
        // Ensure access_cabang_ids is an array
        return in_array($branchId, $user->access_cabang_ids ?? []);
    }

    return false;
});