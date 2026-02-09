<?php

declare(strict_types=1);

namespace SapB1\Toolkit\Filament\Policies;

use Illuminate\Foundation\Auth\User;
use SapB1\Toolkit\Models\AuditLog;

class AuditLogPolicy extends SapB1Policy
{
    public function viewAny(User $user): bool
    {
        return $this->checkGate($user, 'audit.viewAny');
    }

    public function view(User $user, AuditLog $auditLog): bool
    {
        return $this->checkGate($user, 'audit.view');
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, AuditLog $auditLog): bool
    {
        return false;
    }

    public function delete(User $user, AuditLog $auditLog): bool
    {
        return $this->checkGate($user, 'audit.delete');
    }

    public function export(User $user): bool
    {
        return $this->checkGate($user, 'audit.export');
    }
}
