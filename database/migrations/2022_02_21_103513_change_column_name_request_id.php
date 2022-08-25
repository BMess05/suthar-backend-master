<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeColumnNameRequestId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('redeem_reqs', function (Blueprint $table) {
            $table->renameColumn('bank_id', 'req_id');
            // $table->foreignId('req_id')->comment=("bank_id or order_id")->change();
            // $table->tinyInteger('req_type')->comment=("1=>Cash,2=>Account,3=>GiftsOrder")->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('redeem_reqs', function (Blueprint $table) {
            $table->renameColumn('req_id', 'bank_id')->comment="";
        });
    }
}
