<?php

namespace App\Http\Controllers;
use App\Http\Models\payment;
use Illuminate\Http\Request;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
class PayPalController extends Controller
{

    public function createTransaction(Request $request)
    {
        $reservation_id = $request->reservation_id ?? 1; 
        $amount = $request->amount ?? "10.00";

        session(['reservation_id' => $reservation_id, 'amount' => $amount]);

        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $paypalToken = $provider->getAccessToken();

        $response = $provider->createOrder([
            "intent" => "CAPTURE",
            "application_context" => [
                "return_url" => route('successTransaction'),
                "cancel_url" => route('cancelTransaction'),
            ],
            "purchase_units" => [
                0 => [
                    "amount" => [
                        "currency_code" => "USD",
                        "value" => $amount
                    ]
                ]
            ]
        ]);

        if (isset($response['id']) && $response['id'] != null) {
            foreach ($response['links'] as $links) {
                if ($links['rel'] == 'approve') {
                    return redirect()->away($links['href']);
                }
            }

            return redirect()
                ->back()
                ->with('error', 'Something went wrong.');
        } else {
            return redirect()
                ->back()
                ->with('error', $response['message'] ?? 'Something went wrong.');
        }
    }

    public function successTransaction(Request $request)
    {
        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $provider->getAccessToken();
        $response = $provider->capturePaymentOrder($request['token']);

        if (isset($response['status']) && $response['status'] == 'COMPLETED') {

            $reservation_id = session('reservation_id');
            $amount = session('amount');

            // Create payment record
            \App\Models\Payment::create([
                'reservation_id' => $reservation_id,
                'amount' => $amount,
                'status' => 'completed',
                'payment_method' => 'paypal',
                'transaction_id' => $response['id'],
            ]);

            // Update reservation status
            $reservation = \App\Models\reservation::find($reservation_id);
            if ($reservation) {
                $reservation->update([
                    'status' => 'accepted',
                    'paid_at' => now(),
                ]);
            }

            // Clear session
            session()->forget(['reservation_id', 'amount']);

            return response()->json([
                'status' => 'success',
                'message' => 'Transaction complete.',
                'transaction_id' => $response['id']
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => $response['message'] ?? 'Something went wrong.'
            ]);
        }
    }

    public function cancelTransaction(Request $request)
    {
        return response()->json([
            'status' => 'error',
            'message' => 'You have canceled the transaction.'
        ]);
    }
}
