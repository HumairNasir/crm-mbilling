<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDentalOfficesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dental_offices', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();

            // Foreign Keys (Nullable for bulk import safety)
            $table->unsignedBigInteger('sales_rep_id')->nullable();
            $table->string('country')->nullable();
            $table->unsignedBigInteger('region_id')->nullable();
            $table->unsignedBigInteger('state_id')->nullable();
            $table->unsignedBigInteger('territory_id')->nullable();

            $table->string('contacted_source')->nullable();
            $table->string('receptive')->nullable();
            $table->string('purchase_product')->nullable();

            // Using string to match your dump format perfectly
            $table->string('follow_up_date')->nullable();
            $table->string('contact_date')->nullable();

            $table->string('contact_person')->nullable();
            $table->longText('description')->nullable();

            // New Columns from your SQL Dump
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('address')->nullable();
            $table->string('purchase_subscriptions')->nullable();
            $table->string('dr_name')->nullable();

            $table->timestamps();
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
        Schema::dropIfExists('dental_offices');
    }
}
