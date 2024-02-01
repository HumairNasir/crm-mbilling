<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->string('sale_value')->nullable();
            $table->unsignedBigInteger('sales_rep_id')->nullable();
            $table->unsignedBigInteger('dental_office_id')->nullable();
            $table->foreign('sales_rep_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('dental_office_id')->references('id')->on('dental_offices')->onDelete('set null');
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
        Schema::dropIfExists('sales');
    }
}
