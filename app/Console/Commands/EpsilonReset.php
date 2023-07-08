<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\User;
use App\Gift;
use App\LotteryTicket;
use App\Transfert;

use App\Http\Middleware\FillServers;

class EpsilonReset extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'epsilon_reset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset Ogrines, Tickets and Jetons for Epsilon';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // Load servers config
        $serverFiller = new FillServers();
        $serverFiller->handle(null, function () {
        });

        // Delete all gifts for Epislon
        Gift::where('server', 'epsilon')->delete();

        // Restor lottery ticket for Epsilon
        $tickets = LotteryTicket::where('server', 'epsilon')->get();

        foreach ($tickets as $ticket) {
            $ticket->used    = false;
            $ticket->server  = null;
            $ticket->item_id = null;
            $ticket->max     = false;
            $ticket->save();
        }

        // Refund Ogrines
        $transfertsOgrines = Transfert::where('server', 'epsilon')->where('state', 1)->where('type', config('dofus.details')['epsilon']->ogrine)->get();

        foreach ($transfertsOgrines as $transfert) {
            $user = $transfert->user();
            $user->points += $transfert->amount;
            $user->save();
        }

        // Refund Jetons
        $transfertsJetons = Transfert::where('server', 'epsilon')->where('state', 1)->where('type', 'Ogrines')->get();

        foreach ($transfertsJetons as $transfert) {
            $user = $transfert->user();
            $user->jetons += $transfert->amount / config('dofus.points_by_vote');
            $user->save();
        }

        // Delete all transfert for Epsilon
        Transfert::where('server', 'epsilon')->delete();
    }
}
