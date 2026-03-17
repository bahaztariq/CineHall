<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\reservation;

class DeleteExpiredReservations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:delete-expired-reservations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $reserevation = reservation::where('status','pending')
            ->where('reserved_at', '<', now())
            ->delete();

        $this->info("Deleted {$reserevation} expired Reservation.");
    }
}
