<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIterationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('iterations', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable(); // e.g., "Batch 1 - Feb 2026"
            $table->integer('batch_size')->default(5000); // How many leads in this batch
            $table->string('status')->default('active'); // active, completed
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable(); // Projected end date
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
        Schema::dropIfExists('iterations');
    }
}
