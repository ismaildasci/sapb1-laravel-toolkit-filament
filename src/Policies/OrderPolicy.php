<?php

declare(strict_types=1);

namespace SapB1\Toolkit\Filament\Policies;

use Illuminate\Foundation\Auth\User;
use SapB1\Toolkit\Models\Sales\Order;

class OrderPolicy extends SapB1Policy
{
    public function viewAny(User $user): bool
    {
        return $this->checkGate($user, 'order.viewAny');
    }

    public function view(User $user, Order $order): bool
    {
        return $this->checkGate($user, 'order.view');
    }

    public function create(User $user): bool
    {
        return $this->checkGate($user, 'order.create');
    }

    public function update(User $user, Order $order): bool
    {
        return $this->checkGate($user, 'order.update');
    }

    public function delete(User $user, Order $order): bool
    {
        return $this->checkGate($user, 'order.delete');
    }

    public function close(User $user, Order $order): bool
    {
        return $this->checkGate($user, 'order.close');
    }

    public function cancel(User $user, Order $order): bool
    {
        return $this->checkGate($user, 'order.cancel');
    }

    public function copyToDelivery(User $user, Order $order): bool
    {
        return $this->checkGate($user, 'order.copyToDelivery');
    }

    public function copyToInvoice(User $user, Order $order): bool
    {
        return $this->checkGate($user, 'order.copyToInvoice');
    }
}
