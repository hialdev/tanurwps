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
        Schema::create('workspaces', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('agent_id');
            $table->string('code')->unique();

            $table->string('name');
            $table->text('description')->nullable();
            $table->text('address');
            $table->string('city');
            $table->string('postal_code');

            $table->string('pic_name');
            $table->string('pic_phone');
            $table->string('pic_email')->nullable();

            $table->string('product_type')->nullable();

            $table->enum('status', [0,1,2,3,4,5])->default(0); // 0 : Pending, 1 : Berjalan, 2 : Pengajuan Stage, 3 : Stage Ditolak, 4 : Selesai, 5: Ditolak
            $table->timestamp('finished_at')->nullable(); // Terisi saat pengajuan, null kembali jika pengajuan ditolak
            $table->timestamp('approved_at')->nullable(); // Terisi saat semua selesai diterima

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
        Schema::dropIfExists('workspaces');
    }
};
