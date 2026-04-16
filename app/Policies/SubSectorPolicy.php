<?php

namespace App\Policies;

use App\Models\SubSector;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class SubSectorPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('sub_sector-view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, SubSector $subSector): bool
    {
        return $user->can('sub_sector-view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('sub_sector-create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, SubSector $subSector): bool
    {
        return $user->can('sub_sector-update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, SubSector $subSector): bool
    {
        return $user->can('sub_sector-delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, SubSector $subSector): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, SubSector $subSector): bool
    {
        return false;
    }
}
