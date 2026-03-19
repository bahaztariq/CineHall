<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\reservation;
use App\Models\ticket;
use App\Services\ReservationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

class PayPalController extends Controller
{
    protected $reservationService;

    public function __construct(ReservationService $reservationService)
    {
        $this->reservationService = $reservationService;
    }

    public function createTransaction(Request $request)
    {
        $request->validate([
            'reservation_id' => 'required|exists:reservations,id',
            'amount'         => 'required|numeric|min:0.01',
        ]);

        $reservation_id = $request->reservation_id;
        $amount         = $request->amount;

        // Security check: reservation must belong to the authenticated user
        $reservation = reservation::where('id', $reservation_id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$reservation) {
            return response()->json(['error' => 'Unauthorized or invalid reservation.'], 403);
        }

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

    public function successTransaction(Request $request)
    {
        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $provider->getAccessToken();

        $response = $provider->capturePaymentOrder($request['token']);

        if (isset($response['status']) && $response['status'] == 'COMPLETED') {
            $reservation_id = $request->reservation_id;
            $amount         = $request->amount;
            $transaction_id = $response['id'];

            // Create payment record (idempotent)
            Payment::updateOrCreate(
                ['transaction_id' => $transaction_id],
                [
                    'reservation_id' => $reservation_id,
                    'amount'         => $amount,
                    'status'         => 'completed',
                    'payment_method' => 'paypal',
                    'transaction_id' => $transaction_id,
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

    public function cancelTransaction(Request $request)
    {
        return response()->json([
            'status'  => 'error',
            'message' => 'You have canceled the transaction.',
        ]);
    }
}
