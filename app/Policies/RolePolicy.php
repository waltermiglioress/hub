<?php

namespace App\Policies;

use App\Helpers\Utils;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Auth\Access\HandlesAuthorization;

class RolePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny($user): bool
    {
       return Utils::checkPermission($user,'view_any_role');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view( $user, Role $role): bool
    {
        return Utils::checkPermission($user,'view_role');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create( $user): bool
    {
        return Utils::checkPermission($user,'create_role');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update( $user, Role $role): bool
    {
        return Utils::checkPermission($user,'update_role');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete( $user, Role $role): bool
    {
        return Utils::checkPermission($user,'delete_role');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny( $user): bool
    {
        return Utils::checkPermission($user,'delete_any_role');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete( $user, Role $role): bool
    {
        return Utils::checkPermission($user,'{{ ForceDelete }}');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny( $user): bool
    {
        return Utils::checkPermission($user,'{{ ForceDeleteAny }}');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore( $user, Role $role): bool
    {
        return Utils::checkPermission($user,'{{ Restore }}');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny( $user): bool
    {
        return Utils::checkPermission($user,'{{ RestoreAny }}');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate( $user, Role $role): bool
    {
        return Utils::checkPermission($user,'{{ Replicate }}');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder( $user): bool
    {
        return Utils::checkPermission($user,'{{ Reorder }}');
    }
}
