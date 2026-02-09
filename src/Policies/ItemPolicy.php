<?php

declare(strict_types=1);

namespace SapB1\Toolkit\Filament\Policies;

use Illuminate\Foundation\Auth\User;
use SapB1\Toolkit\Models\Inventory\Item;

class ItemPolicy extends SapB1Policy
{
    public function viewAny(User $user): bool
    {
        return $this->checkGate($user, 'item.viewAny');
    }

    public function view(User $user, Item $item): bool
    {
        return $this->checkGate($user, 'item.view');
    }

    public function create(User $user): bool
    {
        return $this->checkGate($user, 'item.create');
    }

    public function update(User $user, Item $item): bool
    {
        return $this->checkGate($user, 'item.update');
    }

    public function delete(User $user, Item $item): bool
    {
        return $this->checkGate($user, 'item.delete');
    }

    public function checkStock(User $user): bool
    {
        return $this->checkGate($user, 'item.checkStock');
    }
}
