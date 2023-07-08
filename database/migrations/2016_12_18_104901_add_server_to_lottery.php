<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddServerToLottery extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lottery_items', function (Blueprint $table) {
            $table->string('server')->default("sigma")->after('item_id');
        });

        Schema::table('lottery_tickets', function (Blueprint $table) {
            $table->string('server')->nullable()->after('item_id');
        });

        Schema::table('gifts', function (Blueprint $table) {
            $table->string('server')->default("sigma")->after('item_id');
        });

        \DB::statement('UPDATE lottery_tickets SET server = "sigma" WHERE item_id IS NOT NULL');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lottery_items', function (Blueprint $table) {
            $table->dropColumn('server');
        });

        Schema::table('lottery_tickets', function (Blueprint $table) {
            $table->dropColumn('server');
        });

        Schema::table('gifts', function (Blueprint $table) {
            $table->dropColumn('server');
        });
    }
}
