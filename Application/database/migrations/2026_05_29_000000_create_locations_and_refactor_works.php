<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Crear tabla locations
        Schema::create('locations', function (Blueprint $table) {
            $table->increments('location_id');
            $table->unsignedInteger('client_id');
            $table->string('location_name');
            $table->text('location_description')->nullable();
            $table->string('location_city')->nullable();
            $table->string('location_address');
            $table->string('location_reference')->nullable();
            $table->decimal('location_lat', 10, 8)->nullable();
            $table->decimal('location_lng', 11, 8)->nullable();
            $table->tinyInteger('active')->default(1);
            $table->timestamps();
        });

        // 2. Agregar nuevos campos a works
        Schema::table('works', function (Blueprint $table) {
            $table->unsignedInteger('location_id')->nullable()->after('work_id');
            $table->date('start_date')->nullable()->after('active');
            $table->date('end_date')->nullable()->after('start_date');
            $table->string('work_status')->default('pending')->after('end_date');
        });

        // 3. Migrar datos existentes: crear una location por cada work
        $works = DB::table('works')->get();
        foreach ($works as $work) {
            $originalName = trim($work->work_name);
            $parts = explode(' - ', $originalName, 2);
            
            if (count($parts) === 2) {
                $locationName = trim($parts[0]);
                $workName = trim($parts[1]);
            } else {
                // Fallback por si usan un solo guion sin espacios
                $parts = explode('-', $originalName, 2);
                if (count($parts) === 2) {
                    $locationName = trim($parts[0]);
                    $workName = trim($parts[1]);
                } else {
                    $locationName = $originalName;
                    $workName = $originalName;
                }
            }

            $locationId = DB::table('locations')->insertGetId([
                'client_id' => $work->client_id,
                'location_name' => $locationName,
                'location_description' => null,
                'location_city' => $work->work_city,
                'location_address' => $work->work_address ?? '',
                'location_reference' => $work->work_reference,
                'location_lat' => $work->work_lat,
                'location_lng' => $work->work_lng,
                'active' => 1,
                'created_at' => $work->created_at,
                'updated_at' => $work->updated_at,
            ]);

            DB::table('works')
                ->where('work_id', $work->work_id)
                ->update([
                    'location_id' => $locationId,
                    'work_name' => $workName
                ]);
        }

        // 4. Eliminar columnas viejas de works
        Schema::table('works', function (Blueprint $table) {
            $table->dropColumn([
                'client_id',
                'work_city',
                'work_address',
                'work_reference',
                'work_lat',
                'work_lng',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Re-agregar columnas a works
        Schema::table('works', function (Blueprint $table) {
            $table->bigInteger('client_id')->after('work_id');
            $table->string('work_city')->nullable()->after('work_description');
            $table->string('work_address')->nullable()->after('work_city');
            $table->string('work_reference')->nullable()->after('work_address');
            $table->decimal('work_lat', 10, 8)->nullable()->after('work_reference');
            $table->decimal('work_lng', 11, 8)->nullable()->after('work_lat');
        });

        // Restaurar datos desde locations
        $works = DB::table('works')->get();
        foreach ($works as $work) {
            $location = DB::table('locations')->where('location_id', $work->location_id)->first();
            if ($location) {
                DB::table('works')->where('work_id', $work->work_id)->update([
                    'work_name' => $location->location_name . ' - ' . $work->work_name,
                    'client_id' => $location->client_id,
                    'work_city' => $location->location_city,
                    'work_address' => $location->location_address,
                    'work_reference' => $location->location_reference,
                    'work_lat' => $location->location_lat,
                    'work_lng' => $location->location_lng,
                ]);
            }
        }

        Schema::table('works', function (Blueprint $table) {
            $table->dropColumn(['location_id', 'start_date', 'end_date', 'work_status']);
        });

        Schema::dropIfExists('locations');
    }
};
