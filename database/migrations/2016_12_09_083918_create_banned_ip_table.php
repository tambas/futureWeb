<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBannedIpTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('banned_ip', function (Blueprint $table) {
            $table->increments('id');
            $table->string('description');
            $table->string('begin');
            $table->string('end');
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->boolean('shadowBan')->default(false)->after('banReason');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('banned_ip');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('shadowBan');
        });
    }
}
