<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\User;

// Channel untuk user yang sudah login
Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Channel untuk superadmin - PASTIKAN HANYA SUPERADMIN
Broadcast::channel('superadmin-notify', function (User $user) {
    // Pastikan user adalah superadmin
    if ($user->role !== 'superadmin') {
        \Log::warning('Non-superadmin attempted to access superadmin-notify', [
            'user_id' => $user->id,
            'role' => $user->role
        ]);
        return false;
    }
    
    \Log::info('Superadmin granted access to superadmin-notify', [
        'user_id' => $user->id,
        'name' => $user->nama_lengkap
    ]);
    
    return true;
});

// Channel untuk branch notification
Broadcast::channel('branch-notify.{branchId}', function (User $user, $branchId) {
    // Superadmin bisa akses semua branch
    if ($user->role === 'superadmin') {
        return true;
    }
    
    // Audit hanya bisa akses branch yang ditentukan
    if ($user->role === 'audit') {
        $allowedBranches = is_array($user->access_cabang_ids) 
            ? $user->access_cabang_ids 
            : json_decode($user->access_cabang_ids ?? '[]', true);
            
        return in_array((int) $branchId, $allowedBranches);
    }
    
    return false;
});