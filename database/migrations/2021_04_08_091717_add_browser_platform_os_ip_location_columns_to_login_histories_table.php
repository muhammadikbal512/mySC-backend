<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBrowserPlatformOsIpLocationColumnsToLoginHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('login_histories', function (Blueprint $table) {
            $table->string('browser')->nullable();
            $table->string('platform')->nullable();
            $table->string('os')->nullable();
            $table->string('ip')->nullable();
            $table->bigInteger('location_id')->unsigned()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('login_histories', function (Blueprint $table) {
            //
        });
    }
}
