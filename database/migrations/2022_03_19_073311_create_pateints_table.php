<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePateintsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pateints', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('disease');
            $table->text('diagnosis');
            $table->tinyInteger('is_admitted');
            $table->tinyInteger('is_active');
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
        Schema::dropIfExists('pateints');
    }
}