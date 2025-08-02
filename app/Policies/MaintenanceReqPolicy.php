<?php

namespace App\Policies;

use App\Models\MaintenanceReq;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class MaintenanceReqPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['supervisor', 'chief_engineering','staff']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, MaintenanceReq $maintenanceReq): bool
    {
        return in_array($user->role, ['supervisor', 'chief_engineering']);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->role == 'staff';
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, MaintenanceReq $maintenanceReq): bool
    {
        return $user->role == 'supervisor';
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, MaintenanceReq $maintenanceReq): bool
    {
        return $user->role == 'supervisor';
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, MaintenanceReq $maintenanceReq): bool
    {
        return $user->role == 'supervisor';
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, MaintenanceReq $maintenanceReq): bool
    {
        return $user->role == 'supervisor';
    }
}
