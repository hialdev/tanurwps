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
        Schema::create('stage_task_attachments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('stage_task_id');
            
            $table->string('name');
            $table->text('file');
            $table->string('mime')->nullable();
            
            $table->timestamps();

            $table->foreign('stage_task_id')
                ->references('id')
                ->on('stage_tasks')
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
        Schema::dropIfExists('stage_task_attachments');
    }
};
