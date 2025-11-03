<?php

namespace App\Policies;

use App\User;
use App\ServiceBooking;

class ServiceBookingPolicy
{
    /**
     * Determine whether the user can view any service bookings.
     */
    public function viewAny(User $user)
    {
        // Allow either the legacy permission or the invoice-specific permission
        return $user->can('view bookings') || $user->can('view invoices');
    }

    /**
     * Determine whether the user can view the service booking.
     */
    public function view(User $user, ServiceBooking $serviceBooking)
    {
        // Allow viewing when user has either permission and belongs to the same company
        return ( ($user->can('view bookings') || $user->can('view invoices'))
            && $user->company_id === $serviceBooking->company_id );
    }

    /**
     * Determine whether the user can create service bookings.
     */
    public function create(User $user)
    {
        return $user->can('create bookings');
    }

    /**
     * Determine whether the user can update the service booking.
     */
    public function update(User $user, ServiceBooking $serviceBooking)
    {
        return $user->can('edit bookings') && $user->company_id === $serviceBooking->company_id;
    }

    /**
     * Determine whether the user can delete the service booking.
     */
    public function delete(User $user, ServiceBooking $serviceBooking)
    {
        return $user->can('delete bookings') && $user->company_id === $serviceBooking->company_id;
    }
}
