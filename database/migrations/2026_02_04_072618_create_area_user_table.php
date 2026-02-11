<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAreaUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // We call this 'state_user' usually (singular_singular),
        // but 'area_user' is fine if you prefer that name.
        // Let's stick to your request for 'area_user' or we can name it 'state_user'.
        // Standard Laravel naming convention for a pivot between User and State is 'state_user'.

        Schema::create('state_user', function (Blueprint $table) {
            $table->id();

            // Link to the User (Manager or Rep)
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Link to the State
            // We reference the 'states' table you showed in the image
            $table->foreignId('state_id')->constrained('states')->onDelete('cascade');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('state_user');
    }
}
