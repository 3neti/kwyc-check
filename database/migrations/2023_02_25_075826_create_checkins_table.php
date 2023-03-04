<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('checkins', function (Blueprint $table) {
            $table->uuid();
            $table->unsignedBigInteger('agent_id');
            $table->foreignId('campaign_id')->nullable();
            $table->nullableMorphs('person');
            $table->double('longitude')->nullable();
            $table->double('latitude')->nullable();
            $table->string('url', 2048)->nullable();
            $table->json('data')->nullable();
            $table->foreign('agent_id')->references('id')->on('users');
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
        Schema::dropIfExists('checkins');
    }
};
