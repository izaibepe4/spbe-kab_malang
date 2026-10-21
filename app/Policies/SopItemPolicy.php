<?php

namespace App\Policies;

use App\Models\SopItem;
use Illuminate\Contracts\Auth\Authenticatable;

class SopItemPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(Authenticatable $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(Authenticatable $user, SopItem $sopItem): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(Authenticatable $user): bool
    {
        return $user instanceof \App\Models\Admin;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(Authenticatable $user, SopItem $sopItem): bool
    {
        return $user instanceof \App\Models\Admin;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Authenticatable $user, SopItem $sopItem): bool
    {
        return $user instanceof \App\Models\Admin;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(Authenticatable $user, SopItem $sopItem): bool
    {
        return $user instanceof \App\Models\Admin;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(Authenticatable $user, SopItem $sopItem): bool
    {
        return $user instanceof \App\Models\Admin;
    }
}
