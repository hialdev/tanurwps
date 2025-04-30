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
        Schema::create('workspace_tasks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('workspace_stage_id');
            $table->uuid('stage_task_id');
            
            $table->integer('score')->nullable();
            $table->text('answer_text');

            $table->enum('status', [0,1,2])->default(0); // 0: Berjalan, 1: Pengajuan, 2: Disetujui
            $table->timestamp('finished_at')->nullable(); // Terisi saat pengajuan, null kembali jika pengajuan ditolak
            
            $table->timestamps();

            $table->foreign('workspace_stage_id')
                ->references('id')
                ->on('workspace_stages')
                ->onDelete('restrict');

            $table->foreign('stage_task_id')
                ->references('id')
                ->on('stage_tasks')
                ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('workspace_tasks');
    }
};
