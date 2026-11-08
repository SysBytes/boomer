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
        Schema::create('time_logs', function (Blueprint $table) {
            $table->increments('time_log_id');
            $table->bigInteger('user_id');
            $table->bigInteger('work_id');

            // Check-in
            $table->timestamp('check_in_time');
            $table->string('check_in_photo'); // Ruta de la imagen guardada en storage
            $table->decimal('check_in_lat', 10, 8); // Latitud
            $table->decimal('check_in_lng', 11, 8); // Longitud

            // Check-out (Nullable porque al hacer check-in aún no existen)
            $table->timestamp('check_out_time')->nullable();
            $table->string('check_out_photo')->nullable();
            $table->decimal('check_out_lat', 10, 8)->nullable();
            $table->decimal('check_out_lng', 11, 8)->nullable();

            // Horas calculadas (Puedes usar un mutator en el modelo, pero guardarlo es útil para reportes)
            $table->decimal('hours_worked', 8, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('time_logs');
    }
};
