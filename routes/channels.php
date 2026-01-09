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

// 1. Channel Superadmin: Hanya user role superadmin yang boleh dengar
Broadcast::channel('superadmin-notify', function (User $user) {
    return $user->role === 'superadmin';
});

// 2. Channel Cabang: Superadmin BOLEH, Audit BOLEH (jika punya akses ke cabang tsb)
Broadcast::channel('branch-notify.{branchId}', function (User $user, $branchId) {
    if ($user->role === 'superadmin') {
        return true;
    }
    
    if ($user->role === 'audit') {
        // Cek apakah ID cabang ada di list akses audit
        return in_array($branchId, $user->access_cabang_ids);
    }

    return false;
});

// 3. Channel Distributor
Broadcast::channel('distributor-notify.{distId}', function (User $user, $distId) {
    return $user->role === 'superadmin'; // Atau tambahkan logika audit jika perlu
});