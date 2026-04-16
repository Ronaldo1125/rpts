<?php

namespace App\Policies;

use App\Models\EndorseYear;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class EndorseYearPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('endorse_year-view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, EndorseYear $endorseYear): bool
    {
        return $user->can('endorse_year-view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('endorse_year-create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, EndorseYear $endorseYear): bool
    {
        return $user->can('endorse_year-update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, EndorseYear $endorseYear): bool
    {
        return $user->can('endorse_year-delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, EndorseYear $endorseYear): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, EndorseYear $endorseYear): bool
    {
        return false;
    }
}
