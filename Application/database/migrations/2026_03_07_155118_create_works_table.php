<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('works', function (Blueprint $table) {
            $table->increments('work_id');
            $table->bigInteger('client_id');
            $table->bigInteger('supervisor_id');
            $table->string('work_name');
            $table->string('work_description')->nullable();
            $table->string('work_city')->nullable();
            $table->string('work_address')->nullable();
            $table->string('work_reference')->nullable();
            $table->decimal('work_lat', 10, 8)->nullable(); // Latitud
            $table->decimal('work_lng', 11, 8)->nullable(); // Longitud
            $table->tinyInteger('active')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('works');
    }
};
