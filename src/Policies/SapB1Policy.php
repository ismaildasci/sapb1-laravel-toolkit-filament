<?php

declare(strict_types=1);

namespace SapB1\Toolkit\Filament\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User;

abstract class SapB1Policy
{
    use HandlesAuthorization;

    public function before(User $user): ?bool
    {
        if (! config('sapb1-filament.authorization.enabled', false)) {
            return true;
        }

        $superAdminRole = config('sapb1-filament.authorization.super_admin_role', 'super-admin');

        if (method_exists($user, 'hasRole') && $user->hasRole($superAdminRole)) {
            return true;
        }

        return null;
    }

    protected function checkGate(User $user, string $ability): bool
    {
        $gate = config('sapb1-filament.authorization.gate', 'sapb1-admin');

        if (method_exists($user, 'can')) {
            return $user->can("{$gate}.{$ability}");
        }

        return true;
    }
}
