<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRawInOutToTransferts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transferts', function (Blueprint $table) {
            $table->text('rawIn')->nullable()->after('type');
            $table->text('rawOut')->nullable()->after('rawIn');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transferts', function (Blueprint $table) {
            $table->dropColumn('rawIn');
            $table->dropColumn('rawOut');
        });
    }
}
