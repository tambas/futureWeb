<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMaxToLottery extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lottery_items', function (Blueprint $table) {
            $table->boolean('max')->default(false)->after('item_id');
        });

        Schema::table('lottery_tickets', function (Blueprint $table) {
            $table->boolean('max')->default(false)->after('item_id');
        });

        Schema::table('gifts', function (Blueprint $table) {
            $table->boolean('max')->default(false)->after('item_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lottery_items', function (Blueprint $table) {
            $table->dropColumn('max');
        });

        Schema::table('lottery_tickets', function (Blueprint $table) {
            $table->dropColumn('max');
        });

        Schema::table('gifts', function (Blueprint $table) {
            $table->dropColumn('max');
        });
    }
}
