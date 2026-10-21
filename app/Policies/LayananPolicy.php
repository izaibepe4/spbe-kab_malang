<?php

namespace App\Policies;

use App\Models\Layanan;
use Illuminate\Contracts\Auth\Authenticatable;

class LayananPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(Authenticatable $user): bool
    {
        return true; // Semua user yang login bisa lihat list
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(Authenticatable $user, Layanan $layanan): bool
    {
        // Admin bisa lihat semua, OPD hanya data sendiri
        return $user instanceof \App\Models\Admin || $layanan->user_id === $user->getAuthIdentifier();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(Authenticatable $user): bool
    {
        return true; // Semua user yang login bisa create
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(Authenticatable $user, Layanan $layanan): bool
    {
        // Admin bisa update semua, OPD hanya data sendiri
        return $user instanceof \App\Models\Admin || $layanan->user_id === $user->getAuthIdentifier();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Authenticatable $user, Layanan $layanan): bool
    {
        // Admin bisa delete semua, OPD hanya data sendiri
        return $user instanceof \App\Models\Admin || $layanan->user_id === $user->getAuthIdentifier();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(Authenticatable $user, Layanan $layanan): bool
    {
        return $user instanceof \App\Models\Admin;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(Authenticatable $user, Layanan $layanan): bool
    {
        return $user instanceof \App\Models\Admin;
    }
}
