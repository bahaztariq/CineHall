<?php

namespace App\Services;

use App\Models\reservation;
use App\Models\ticket;

class ReservationService
{
    /**
     * Confirm a payment for a reservation.
     * Updates status to 'accepted', sets 'paid_at', and creates a ticket.
     */
    public function confirmPayment(reservation $reservation)
    {
        if ($reservation->status === 'accepted') {
            return;
        }

        $reservation->update([
            'status'  => 'accepted',
            'paid_at' => now(),
        ]);

        // Auto-create a ticket for the confirmed reservation
        return ticket::firstOrCreate(
            ['reservation_id' => $reservation->id],
            [
                'user_id' => $reservation->user_id,
                'seat_id' => $reservation->seat_id,
            ]
        );
    }
}
