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
        Schema::create('workspace_stage_approvals', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('workspace_stage_id');
            $table->uuid('approver_id');
            
            $table->enum('status', [0,1,2])->default(0); // 0: Waiting, 1: Approved, 2: Rejected
            $table->timestamp('approved_at')->nullable(); // Terisi saat disetujui
            $table->timestamp('rejected_at')->nullable(); // Terisi saat ditolak
            $table->text('reason')->nullable();
            $table->text('attachment')->nullable();
            
            $table->timestamps();

            $table->foreign('workspace_stage_id')
                ->references('id')
                ->on('workspace_stages')
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
        Schema::dropIfExists('workspace_stage_approvals');
    }
};
