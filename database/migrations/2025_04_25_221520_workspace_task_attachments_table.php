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
        Schema::create('workspace_task_attachments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('workspace_task_id');
            
            $table->string('name');
            $table->text('file');
            $table->string('mime')->nullable();
            
            $table->timestamps();

            $table->foreign('workspace_task_id')
                ->references('id')
                ->on('workspace_tasks')
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
        Schema::dropIfExists('workspace_task_attachments');
    }
};
