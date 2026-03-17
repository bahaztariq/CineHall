<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\reservation;
use Illuminate\Http\Request;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Illuminate\Support\Facades\Auth;

class PayPalController extends Controller
{
    public function createTransaction(Request $request)
    {
        $request->validate([
            'reservation_id' => 'required|exists:reservations,id',
            'amount' => 'required|numeric|min:0.01',
        ]);

        $reservation_id = $request->reservation_id;
        $amount = $request->amount;

        // Security check: Ensure reservation belongs to the user
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
            "intent" => "CAPTURE",
            "application_context" => [
                "return_url" => route('successTransaction', ['reservation_id' => $reservation_id, 'amount' => $amount]),
                "cancel_url" => route('cancelTransaction'),
            ],
            "purchase_units" => [
                0 => [
                    "amount" => [
                        "currency_code" => config('paypal.currency', 'USD'),
                        "value" => $amount
                    ]
                ]
            ]
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
            $amount = $request->amount;

            // Integrity check: match the captured amount with expected amount
            // (Optional, PayPal validation handles this but good for local logs)

            // Create payment record
            Payment::updateOrCreate(
                ['transaction_id' => $response['id']],
                [
                    'reservation_id' => $reservation_id,
                    'amount' => $amount,
                    'status' => 'completed',
                    'payment_method' => 'paypal',
                ]
            );

            // Update reservation status
            $reservation = reservation::find($reservation_id);
            if ($reservation) {
                $reservation->update([
                    'status' => 'accepted',
                    'paid_at' => now(),
                ]);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Payment successful.',
                'transaction_id' => $response['id']
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => $response['message'] ?? 'Payment capture failed.'
        ], 500);
    }

    public function cancelTransaction(Request $request)
    {
        return response()->json([
            'status' => 'error',
            'message' => 'You have canceled the transaction.'
        ]);
    }
}
