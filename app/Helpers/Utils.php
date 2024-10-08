<?php

namespace App\Helpers;

use App\Models\User;
use App\Models\CliFor;

class Utils
{
    /**
     * Verifica se l'utente (User o CliFor) ha il permesso.
     */
    public static function checkPermission($user, string $permission): bool
    {
        // Verifica che l'utente sia un'istanza di User o CliFor
        if ($user instanceof User || $user instanceof CliFor) {
            return $user->can($permission);
        }

        // Se non è né User né CliFor, ritorna false
        return false;
    }
}
