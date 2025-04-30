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
        Schema::create('workspace_stages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('workspace_id');
            $table->uuid('stage_id');
            
            $table->integer('total_score')->nullable();
            $table->integer('reduce_score')->nullable();
            $table->integer('final_score')->nullable();
            $table->date('deadline_at'); // tanggal now() + {deadline_days} hari

            $table->enum('status', [0,1,2])->default(0); // 0: Berjalan, 1: Pengajuan, 2: Disetujui
            $table->timestamp('finished_at')->nullable(); // Terisi saat pengajuan, null kembali jika pengajuan ditolak
            $table->timestamp('approved_at')->nullable(); // Terisi saat semua selesai diterima
            
            $table->timestamps();

            $table->foreign('workspace_id')
                ->references('id')
                ->on('workspaces')
                ->onDelete('restrict');

            $table->foreign('stage_id')
                ->references('id')
                ->on('stages')
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
        Schema::dropIfExists('workspace_stages');
    }
};
