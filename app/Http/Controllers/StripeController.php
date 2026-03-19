<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\reservation;
use App\Models\ticket;
use App\Services\ReservationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StripeController extends Controller
{
    protected $reservationService;

    public function __construct(ReservationService $reservationService)
    {
        $this->reservationService = $reservationService;
    }

    /**
     * Create a Stripe Checkout Session for a reservation.
     */
    public function createSession(Request $request)
    {
        $request->validate([
            'reservation_id' => 'required|exists:reservations,id',
            'amount'         => 'required|numeric|min:1',
        ]);

        $user = Auth::user();
        $reservation = reservation::where('id', $request->reservation_id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        if ($reservation->status === 'accepted') {
            return response()->json(['error' => 'Reservation already paid.'], 400);
        }

        return $user->checkout([$request->amount * 100 => 'Reservation Payment'], [
            'success_url' => route('stripe.success', ['reservation_id' => $reservation->id]) . '&session_id={CHECKOUT_SESSION_ID}',
            'cancel_url'  => route('stripe.cancel'),
            'metadata'    => [
                'reservation_id' => $reservation->id,
                'user_id'        => $user->id,
            ],
        ]);
    }

    /**
     * Handle the successful payment return.
     */
    public function handleSuccess(Request $request)
    {
        $reservation_id = $request->get('reservation_id');
        $session_id     = $request->get('session_id');

        if (!$reservation_id || !$session_id) {
            return response()->json(['error' => 'Invalid request.'], 400);
        }

        $reservation = reservation::with('session')->findOrFail($reservation_id);

        // Prevent duplicate processing if user refreshes the success page
        $existingPayment = Payment::where('transaction_id', $session_id)->first();

        if (!$existingPayment) {
            // Fetch the real price from the film session
            $amount = $reservation->session->price ?? 0;

            Payment::create([
                'reservation_id' => $reservation->id,
                'amount'         => $amount,
                'status'         => 'completed',
                'payment_method' => 'stripe',
                'transaction_id' => $session_id,
            ]);

            $this->reservationService->confirmPayment($reservation);
        }

        return response()->json([
            'status'         => 'success',
            'message'        => 'Payment successful via Stripe.',
            'reservation_id' => $reservation_id,
        ]);
    }

    /**
     * Handle payment cancellation.
     */
    public function handleCancel()
    {
        return response()->json([
            'status'  => 'error',
            'message' => 'Payment was canceled.',
        ]);
    }
}
