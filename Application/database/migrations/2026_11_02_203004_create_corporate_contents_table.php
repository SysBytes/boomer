<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('corporate_contents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subcategory_id')
                ->constrained('document_subcategories')
                ->cascadeOnDelete();
            $table->string('title');
            $table->enum('type', ['pdf', 'video']);
            $table->string('file_path');
            $table->integer('required_time_seconds')->default(0);
            $table->foreignId('target_role_id')
                ->nullable()
                ->constrained('roles')
                ->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('corporate_contents');
    }
};

