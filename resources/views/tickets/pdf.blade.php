<!DOCTYPE html>
<html>
<head>
    <title>Ticket CinéHall</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; text-align: center; }
        .ticket { border: 2px solid #333; padding: 20px; width: 400px; margin: auto; }
        img { margin-top: 15px; }
    </style>
</head>
<body>
    <div class="ticket">
        <h2>CinéHall - Ticket</h2>
        <p><strong>Reservation ID:</strong> {{ $ticket->reservation_id }}</p>
        <p><strong>Seat:</strong> {{ $ticket->seat_number }}</p>
        <p><strong>User ID:</strong> {{ $ticket->user_id }}</p>

        <img src="data:image/png;base64, {{ $qrCode }}" alt="QR Code">
    </div>
</body>
</html>