<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\User;
use App\VoteReward;
use App\Gift;
use App\LotteryTicket;

class VoteRewardDelivery extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vote_reward_delivery';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send vote reward to each users';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $users = User::all();

        foreach ($users as $user) {
            $numberOfTickets = (int)($user->votes / 10);
            $ticketsCounter = 0;

            for ($vote = 10; $vote <= $user->votes; $vote += 10) {
                $description = "Cadeau " . $vote . " votes";

                if (Gift::where('user_id', $user->id)->where('description', $description)->first()) {
                    continue;
                }

                $description = "Ticket " . $vote . " votes";

                if (LotteryTicket::where('user_id', $user->id)->where('description', $description)->first()) {
                    continue;
                }

                $ticket = new LotteryTicket;
                $ticket->type        = $vote % 50 == 0 ? LotteryTicket::GOLD : LotteryTicket::NORMAL;
                $ticket->user_id     = $user->id;
                $ticket->description = $description;
                $ticket->save();

                $ticketsCounter++;
            }

            echo "{$user->pseudo}: ".$ticketsCounter."/$numberOfTickets tickets\n";
        }
    }
}
