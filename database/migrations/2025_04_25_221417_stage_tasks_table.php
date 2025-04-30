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
        Schema::create('stage_tasks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('stage_id');
            
            $table->string('name')->unique();
            $table->integer('order')->nullable();
            $table->text('description')->nullable();
            $table->integer('score')->nullable();
            
            $table->timestamps();

            $table->foreign('stage_id')
                ->references('id')
                ->on('stages')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stage_tasks');
    }
};
