<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableRedeemReqs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('redeem_reqs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contractor_id')->refrences('id')->on('contractors')->onDelete('cascade');
            $table->integer('points')->default(0);
            $table->tinyInteger('req_type')->comment="1=>Cash,2=>Account";
            $table->foreignId('bank_id')->nullable();
            $table->tinyInteger('req_status')->default(0)->comment="0=>pending, 1=>Complete";
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('redeem_reqs');
    }
}
