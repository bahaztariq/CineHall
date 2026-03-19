<?php

namespace App\Http\Controllers;

use App\Models\ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Barryvdh\DomPDF\Facade\Pdf;

class TicketController extends Controller
{
    /**
     * Download the ticket as a PDF receipt.
     */
    public function donwloadReceipt($ticketId)
    {
        $ticket = ticket::with([
            'reservation.session.film',
            'seat',
            'user',
        ])->findOrFail($ticketId);

        // Ensure the ticket belongs to the authenticated user
        if ($ticket->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized.'], 403);
        }

        // Build a payload string for the QR code
        $qrData = implode('|', [
            'TicketID:'      . $ticket->id,
            'ReservationID:' . $ticket->reservation_id,
            'Seat:'          . ($ticket->seat->number ?? $ticket->seat_id),
            'Film:'          . ($ticket->reservation->session->film->title ?? 'N/A'),
        ]);

        // Generate QR code using BaconQrCode (via SimpleQRCode)
        // Switch to SVG to avoid imagick dependency required by PNG
        $qrCode = QrCode::format('svg')
            ->size(200)
            ->errorCorrection('H')
            ->generate($qrData);

        // Load the PDF view
        $pdf = Pdf::loadView('tickets.pdf', [
            'ticket' => $ticket,
            'qrCode' => $qrCode, // SVG string
        ]);

        return $pdf->download("ticket-{$ticket->id}.pdf");
    }
}
