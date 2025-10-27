<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WhatsAppGroup;
use Illuminate\Auth\Access\Response;

class WhatsAppGroupPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, WhatsAppGroup $whatsAppGroup): bool
    {
        return $user->id === $whatsAppGroup->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, WhatsAppGroup $whatsAppGroup): bool
    {
        return $user->id === $whatsAppGroup->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, WhatsAppGroup $whatsAppGroup): bool
    {
        return $user->id === $whatsAppGroup->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, WhatsAppGroup $whatsAppGroup): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, WhatsAppGroup $whatsAppGroup): bool
    {
        return false;
    }
}
