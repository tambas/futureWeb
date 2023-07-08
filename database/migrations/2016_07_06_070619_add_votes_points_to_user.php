<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddVotesPointsToUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function ($table) {
            $table->string('points')->default(0)->after('avatar');
            $table->string('votes')->default(0)->after('points');
            $table->dateTime('last_vote')->after('votes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function ($table) {
            $table->dropColumn('points');
            $table->dropColumn('votes');
            $table->dropColumn('last_vote');
        });
    }
}
