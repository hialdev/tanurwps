<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(Str::uuid());
            $table->string('group');
            $table->string('group_key');
            $table->string('name');
            $table->string('key')->unique();
            $table->text('description')->nullable();
            $table->string('input_type')->default('text');
            $table->text('value')->nullable();
            $table->boolean('is_urgent')->default(0);
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
        Schema::dropIfExists('settings');
    }
};
