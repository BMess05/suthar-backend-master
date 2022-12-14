<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SoftdeleteForContractorsAccount extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contractors', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('notifications', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('orders', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('order_gifts', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('points', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('redeem_reqs', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('references', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contractors', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('orders', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('order_gifts', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('points', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('redeem_reqs', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('references', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
}
