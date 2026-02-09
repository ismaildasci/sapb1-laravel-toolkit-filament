<?php

declare(strict_types=1);

namespace SapB1\Toolkit\Filament\Policies;

use Illuminate\Foundation\Auth\User;
use SapB1\Toolkit\Models\BusinessPartner\Partner;

class PartnerPolicy extends SapB1Policy
{
    public function viewAny(User $user): bool
    {
        return $this->checkGate($user, 'partner.viewAny');
    }

    public function view(User $user, Partner $partner): bool
    {
        return $this->checkGate($user, 'partner.view');
    }

    public function create(User $user): bool
    {
        return $this->checkGate($user, 'partner.create');
    }

    public function update(User $user, Partner $partner): bool
    {
        return $this->checkGate($user, 'partner.update');
    }

    public function delete(User $user, Partner $partner): bool
    {
        return $this->checkGate($user, 'partner.delete');
    }
}
