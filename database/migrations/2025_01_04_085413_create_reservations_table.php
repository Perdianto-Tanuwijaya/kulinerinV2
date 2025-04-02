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
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->unsignedBigInteger('restaurant_id');
            $table->foreign('restaurant_id')->references('id')->on('restaurants');
            $table->unsignedBigInteger('table_restaurant_id');
            $table->foreign('table_restaurant_id')->references('id')->on('table_restaurants')->constrained()->onDelete('cascade');
            $table->integer("guest");
            $table->string("restaurantName")->nullable();
            $table->date("reservationDate");
            $table->time("reservationTime");
            $table->string("reservationStatus");
            $table->string("bookingCode");
            $table->text("menuData")->nullable();
            $table->integer("priceTotal")->nullable();
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
        Schema::dropIfExists('reservations');
    }
};
