<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();

            // Links
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // The Sales Rep
            $table->foreignId('dental_office_id')->constrained('dental_offices')->onDelete('cascade'); // The Lead
            $table->foreignId('iteration_id')->constrained('iterations')->onDelete('cascade'); // The Batch

            // Task Details
            $table->string('status')->default('pending'); // pending, completed, converted
            $table->text('ai_suggested_approach')->nullable(); // The OpenAI script
            $table->timestamp('due_date')->nullable(); // For the Orange Box check
            $table->timestamp('completed_at')->nullable();

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
        Schema::dropIfExists('tasks');
    }
}
