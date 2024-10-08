<?php

namespace App\Policies;


use App\Helpers\Utils;
use App\Models\SubContract;
use Illuminate\Auth\Access\HandlesAuthorization;

class SubContractPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny($user): bool
    {
        return Utils::checkPermission($user,'view_any_sub::contract');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view($user, SubContract $subContract): bool
    {
        return Utils::checkPermission($user,'view_sub::contract');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create($user): bool
    {
        return Utils::checkPermission($user,'create_sub::contract');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update( $user, SubContract $subContract): bool
    {
        return Utils::checkPermission($user,'update_sub::contract');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete( $user, SubContract $subContract): bool
    {
        return Utils::checkPermission($user,'delete_sub::contract');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny( $user): bool
    {
        return Utils::checkPermission($user,'delete_any_sub::contract');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete( $user, SubContract $subContract): bool
    {
        return Utils::checkPermission($user,'force_delete_sub::contract');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny( $user): bool
    {
        return Utils::checkPermission($user,'force_delete_any_sub::contract');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore( $user, SubContract $subContract): bool
    {
        return Utils::checkPermission($user,'restore_sub::contract');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny( $user): bool
    {
        return Utils::checkPermission($user,'restore_any_sub::contract');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate( $user, SubContract $subContract): bool
    {
        return Utils::checkPermission($user,'replicate_sub::contract');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder( $user): bool
    {
        return Utils::checkPermission($user,'reorder_sub::contract');
    }
}
