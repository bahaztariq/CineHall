<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\reservation;
use App\Models\ticket;
use App\Services\ReservationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use OpenApi\Attributes as OA;

class PayPalController extends Controller
{
    protected ReservationService $reservationService;

    public function __construct(ReservationService $reservationService)
    {
        $this->reservationService = $reservationService;
    }

    #[OA\Post(
        path: '/transactions/paypal',
        summary: 'Create a PayPal transaction order',
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
            new OA\Response(response: 200, description: 'PayPal approval URL returned'),
            new OA\Response(response: 403, description: 'Unauthorized/Forbidden'),
            new OA\Response(response: 500, description: 'Internal server error')
        ]
    )]
    public function createTransaction(Request $request)
    {
        $request->validate([
            'reservation_id' => 'required|exists:reservations,id'
        ]);

        $reservation_id = $request->reservation_id;

        // Security check: reservation must belong to the authenticated user
        $reservation = reservation::with('session')->where('id', $reservation_id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$reservation) {
            return response()->json(['error' => 'Unauthorized or invalid reservation.'], 403);
        }

        $amount = $reservation->session->price;

        if ($reservation->status === 'accepted') {
            return response()->json(['error' => 'Reservation already paid.'], 400);
        }

        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $provider->getAccessToken();

        $response = $provider->createOrder([
            "intent"              => "CAPTURE",
            "application_context" => [
                "return_url" => route('successTransaction', ['reservation_id' => $reservation_id, 'amount' => $amount]),
                "cancel_url" => route('cancelTransaction'),
            ],
            "purchase_units" => [
                0 => [
                    "amount" => [
                        "currency_code" => config('paypal.currency', 'USD'),
                        "value"         => $amount,
                    ],
                ],
            ],
        ]);

        if (isset($response['id']) && $response['id'] != null) {
            foreach ($response['links'] as $links) {
                if ($links['rel'] == 'approve') {
                    return response()->json(['approval_url' => $links['href']]);
                }
            }
        }

        return response()->json(['error' => $response['message'] ?? 'Unable to create PayPal order.'], 500);
    }

    #[OA\Get(
        path: '/transactions/paypal/success',
        summary: 'Handle successful PayPal payment',
        tags: ['Payments'],
        parameters: [
            new OA\Parameter(name: 'token', in: 'query', required: true, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'reservation_id', in: 'query', required: true, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'amount', in: 'query', required: true, schema: new OA\Schema(type: 'number'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Payment confirmed'),
            new OA\Response(response: 500, description: 'Capture failed')
        ]
    )]
    public function successTransaction(Request $request)
    {
        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $provider->getAccessToken();

        $response = $provider->capturePaymentOrder($request['token']);

        if (isset($response['status']) && $response['status'] == 'COMPLETED') {
            $reservation_id = $request->reservation_id;
            $reservation    = reservation::with('session')->findOrFail($reservation_id);
            $amount         = $reservation->session->price;
            $transaction_id = $response['id'];

            // Create payment record (idempotent)
            Payment::updateOrCreate(
                ['transaction_id' => $transaction_id],
                [
                    'reservation_id' => $reservation_id,
                    'amount'         => $amount,
                    'status'         => 'completed',
                    'payment_method' => 'paypal',
                ]
            );

            // Update reservation status to accepted
            $reservation = reservation::find($reservation_id);
            if ($reservation) {
                $this->reservationService->confirmPayment($reservation);
            }

            return response()->json([
                'status'         => 'success',
                'message'        => 'Payment successful.',
                'transaction_id' => $transaction_id,
            ]);
        }

        return response()->json([
            'status'  => 'error',
            'message' => $response['message'] ?? 'Payment capture failed.',
        ], 500);
    }

    #[OA\Get(
        path: '/transactions/paypal/cancel',
        summary: 'Handle PayPal payment cancellation',
        tags: ['Payments'],
        responses: [
            new OA\Response(response: 200, description: 'Payment canceled')
        ]
    )]
    public function cancelTransaction(Request $request)
    {
        return response()->json([
            'status'  => 'error',
            'message' => 'You have canceled the transaction.',
        ]);
    }
}
