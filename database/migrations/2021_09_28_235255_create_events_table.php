<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('calendar_id');
            $table->string('title',64);
            $table->string('description',4096)->nullable()->default(NULL);
            $table->enum('type',['arrangement','reminder','task']);
            $table->timestamp('start_dt');
            $table->timestamp('end_dt')->nullable()->default(NULL);
            $table->boolean('all_day')->default(FALSE);
            $table->string('color',8)->nullable()->default(NULL);
            $table->timestamps();
            $table->foreign('calendar_id')->references('id')->on('calendars')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('events');
    }
}
