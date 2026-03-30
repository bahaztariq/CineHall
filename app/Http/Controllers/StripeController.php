<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\reservation;
use App\Models\ticket;
use App\Services\ReservationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OA;

class StripeController extends Controller
{
    protected ReservationService $reservationService;

    public function __construct(ReservationService $reservationService)
    {
        $this->reservationService = $reservationService;
    }

    #[OA\Post(
        path: '/transactions/stripe',
        summary: 'Create a Stripe Checkout Session',
        tags: ['Payments'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['reservation_id', 'amount'],
                properties: [
                    new OA\Property(property: 'reservation_id', type: 'integer'),
                    new OA\Property(property: 'amount', type: 'number')
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Stripe session created'),
            new OA\Response(response: 400, description: 'Invalid request or already paid'),
            new OA\Response(response: 401, description: 'Unauthenticated')
        ]
    )]
    public function createSession(Request $request)
    {
        $request->validate([
            'reservation_id' => 'required|exists:reservations,id'
        ]);

        $user = Auth::user();
        $reservation = reservation::with('session')->where('id', $request->reservation_id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        if ($reservation->status === 'accepted') {
            return response()->json(['error' => 'Reservation already paid.'], 400);
        }

        $amount = $reservation->session->price;

        return $user->checkout([$amount * 100 => 'Reservation Payment'], [
            'success_url' => route('stripe.success', ['reservation_id' => $reservation->id]) . '&session_id={CHECKOUT_SESSION_ID}',
            'cancel_url'  => route('stripe.cancel'),
            'metadata'    => [
                'reservation_id' => $reservation->id,
                'user_id'        => $user->id,
            ],
        ]);
    }

    #[OA\Get(
        path: '/transactions/stripe/success',
        summary: 'Handle successful Stripe payment',
        tags: ['Payments'],
        parameters: [
            new OA\Parameter(name: 'reservation_id', in: 'query', required: true, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'session_id', in: 'query', required: true, schema: new OA\Schema(type: 'string'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Payment confirmed'),
            new OA\Response(response: 400, description: 'Invalid request')
        ]
    )]
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

    #[OA\Get(
        path: '/transactions/stripe/cancel',
        summary: 'Handle Stripe payment cancellation',
        tags: ['Payments'],
        responses: [
            new OA\Response(response: 200, description: 'Payment canceled')
        ]
    )]
    public function handleCancel()
    {
        return response()->json([
            'status'  => 'error',
            'message' => 'Payment was canceled.',
        ]);
    }
}
