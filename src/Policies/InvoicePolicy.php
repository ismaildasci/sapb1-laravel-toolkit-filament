<?php

declare(strict_types=1);

namespace SapB1\Toolkit\Filament\Policies;

use Illuminate\Foundation\Auth\User;
use SapB1\Toolkit\Models\Sales\Invoice;

class InvoicePolicy extends SapB1Policy
{
    public function viewAny(User $user): bool
    {
        return $this->checkGate($user, 'invoice.viewAny');
    }

    public function view(User $user, Invoice $invoice): bool
    {
        return $this->checkGate($user, 'invoice.view');
    }

    public function create(User $user): bool
    {
        return $this->checkGate($user, 'invoice.create');
    }

    public function update(User $user, Invoice $invoice): bool
    {
        return $this->checkGate($user, 'invoice.update');
    }

    public function delete(User $user, Invoice $invoice): bool
    {
        return $this->checkGate($user, 'invoice.delete');
    }

    public function cancel(User $user, Invoice $invoice): bool
    {
        return $this->checkGate($user, 'invoice.cancel');
    }

    public function createCreditNote(User $user, Invoice $invoice): bool
    {
        return $this->checkGate($user, 'invoice.createCreditNote');
    }

    public function recordPayment(User $user, Invoice $invoice): bool
    {
        return $this->checkGate($user, 'invoice.recordPayment');
    }
}
