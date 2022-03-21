<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReservationsTable extends Migration
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
            $table->foreignId('doctor_id')->constrained('doctors');
            $table->foreignId('pateint_id')->constrained('pateints');
            $table->foreignId('room_id')->constrained('operation_theatres');
            $table->date('ot_start_date');
            $table->date('ot_end_date');
            $table->time('ot_start_time');
            $table->time('ot_end_time');
            $table->integer('ot_duration');
            $table->integer('ot_status');
            $table->tinyInteger('is_ot_confirm');
            $table->tinyInteger('is_active');
            $table->dateTime('bookingTime');
            $table->softDeletes();
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
}