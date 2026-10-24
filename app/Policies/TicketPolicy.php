<?php

namespace App\Policies;

use App\User;
use App\Models\Ticket;

class TicketPolicy
{
    public function viewAny(User $user)
    {
        return true;
    }

    public function view(User $user, Ticket $ticket)
    {
        return $user->company_id === $ticket->company_id && 
               ($user->hasRole('admin') || $user->id === $ticket->user_id || $user->id === $ticket->assigned_to);
    }

    public function create(User $user)
    {
        return true;
    }

    public function update(User $user, Ticket $ticket)
    {
        return $user->company_id === $ticket->company_id && 
               ($user->hasRole('admin') || $user->id === $ticket->user_id || $user->id === $ticket->assigned_to);
    }

    public function delete(User $user, Ticket $ticket)
    {
        return $user->company_id === $ticket->company_id && $user->hasRole('admin');
    }
}
