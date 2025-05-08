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
        Schema::create('histories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('agent_id');
            $table->uuid('relation_id')->nullable();
            
            $table->enum('type', ['general','workspace', 'workspace_approval', 'stage', 'stage_approval', 'task'])->default('general');
            $table->text('message')->nullable();
            $table->enum('color', ['danger', 'success', 'warning', 'dark'])->nullable();
            
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
        Schema::dropIfExists('histories');
    }
};
