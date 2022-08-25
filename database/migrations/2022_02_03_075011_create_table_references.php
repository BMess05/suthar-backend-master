<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableReferences extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('references', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('phone_number')->nullable();
            $table->string('email')->nullable();
            $table->string('building_type');
            $table->string('state');
            $table->string('city');
            $table->string('address')->nullable();
            $table->string('landmark')->nullable();
            $table->integer('area_in_sqft')->nullable();
            $table->integer('frames_count')->nullable();
            $table->foreignId('store_id');
            $table->string('status')->comment="Pending, Rejected, Accepted, Progress";
            $table->foreignId('created_by');
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
        Schema::dropIfExists('references');
    }
}
