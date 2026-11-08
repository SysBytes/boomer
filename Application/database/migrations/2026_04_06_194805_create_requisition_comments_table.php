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
        Schema::create('requisition_comments', function (Blueprint $table) {
            $table->increments('comment_id');
            $table->bigInteger('requisition_id');
            $table->bigInteger('user_id');
            $table->text('comment');
            $table->timestamps(); // Almacena automáticamente la fecha y hora de creación y actualización
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requisition_comments');
    }
};
