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
        Schema::create('memory_summary', function (Blueprint $table) {
            $table->id();
			$table->unsignedBigInteger('memory_id');
			$table->unsignedBigInteger('summary_id');
            $table->timestamps();

			$table->foreign('memory_id')->references('id')->on('memories')->onDelete('cascade');
			$table->foreign('summary_id')->references('id')->on('summaries')->onDelete('cascade');

			$table->unique(['memory_id', 'summary_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('memory_summary');
    }
};
