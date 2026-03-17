<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreticketRequest;
use App\Http\Requests\UpdateticketRequest;
use App\Models\ticket;

class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
     public function donwloadReceipt($ticketId)
    {   
        $ticket = Ticket::findOrFail($ticketId);

        // generation de qr code
        $qrCode = base64_encode(Qrcode::format('png')
                ->size(200)
                ->generate("ReservationID:{$ticket->reservation_id}|Seat:{$ticket->seat_id}")
        );


        // genere le pdf ave la vue
        $pdf = Pdf::loadView('tickets.pdf',[
            'ticket' => $ticket,
            'qrCode' => $qrCode,
        ]);

    }
    
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreticketRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(ticket $ticket)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ticket $ticket)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateticketRequest $request, ticket $ticket)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ticket $ticket)
    {
        //
    }
}
