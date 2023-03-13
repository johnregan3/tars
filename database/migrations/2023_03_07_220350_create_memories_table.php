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
        Schema::create('memories', function (Blueprint $table) {
			$default_vector = json_encode(array_fill(0, 1536, 0));
            $table->id();
			$table->unsignedBigInteger('speaker_id');
            $table->foreign('speaker_id')->references('id')->on('users');
			$table->longText('content');
            $table->vector('embedding')->default($default_vector);
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
        Schema::dropIfExists('memories');
    }
};
