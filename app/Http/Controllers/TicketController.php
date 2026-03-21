<?php

namespace App\Http\Controllers;

use App\Models\ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Barryvdh\DomPDF\Facade\Pdf;

use OpenApi\Attributes as OA;

class TicketController extends Controller
{
    #[OA\Get(
        path: '/tickets/{ticketId}/download',
        summary: 'Download the ticket as a PDF receipt',
        tags: ['Tickets'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'ticketId', in: 'path', required: true, description: 'Ticket ID', schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'PDF file generated', content: new OA\MediaType(mediaType: 'application/pdf')),
            new OA\Response(response: 403, description: 'Unauthorized'),
            new OA\Response(response: 404, description: 'Ticket not found')
        ]
    )]
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
