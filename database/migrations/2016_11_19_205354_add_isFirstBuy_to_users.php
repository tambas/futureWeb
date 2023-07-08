<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsFirstBuyToUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('isFirstBuy')->default(true)->after('isFirstVote');
        });
    }

     /**
      * Reverse the migrations.
      *
      * @return void
      */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('isFirstBuy');
        });
    }
}
