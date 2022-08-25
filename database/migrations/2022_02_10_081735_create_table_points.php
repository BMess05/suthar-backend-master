<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTablePoints extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('points', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contractor_id');
            $table->tinyInteger('redeem')->comment="0 for credited, 1 for redeemed";
            $table->integer('points');
            $table->foreignId('added_by')->nullable()->comment="credited by store manager or admin, null for redeemed";
            $table->foreignId('reference_id')->nullable()->comment="refrence for which points credited";
            $table->tinyInteger('redeem_type')->nullable()->comment="1=>cash, 2=>bank, 3=>gift";
            $table->foreignId('redeem_id')->nullable()->comment="bank_id or gift_id";
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
        Schema::dropIfExists('points');
    }
}
