<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('country_user', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('country_id');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('country_id')->references('id')->on('countries')->onDelete('cascade');
            
            // Uniqueness
            $table->unique(['user_id', 'country_id']);
        });

        // Migrate existing user country data
        $users = DB::table('users')->get();
        $countries = DB::table('countries')->get();

        foreach ($users as $user) {
            if ($user->country === 'Global') {
                foreach ($countries as $country) {
                    DB::table('country_user')->insert([
                        'user_id' => $user->id,
                        'country_id' => $country->id,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            } else if ($user->country === 'Colombia') {
                DB::table('country_user')->insert([
                    'user_id' => $user->id,
                    'country_id' => 1,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            } else if ($user->country === 'Argentina') {
                DB::table('country_user')->insert([
                    'user_id' => $user->id,
                    'country_id' => 2,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('country_user');
    }
};
