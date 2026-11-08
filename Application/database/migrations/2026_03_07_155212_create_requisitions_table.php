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
        Schema::create('requisitions', function (Blueprint $table) {
            $table->increments('requisition_id');
            $table->bigInteger('user_id'); // Técnico que solicita
            $table->bigInteger('work_id'); // Obra para la que solicita
            $table->enum('status', ['pending', 'approved', 'purchased', 'delivered', 'cancelled', 'rejected'])->default('pending');
            $table->text('reject_reason')->nullable();
            
            $table->bigInteger('approved_by')->nullable(); // Supervisor que aprueba
            $table->timestamp('date_approved')->nullable();
            
            $table->bigInteger('rejected_by')->nullable();
            $table->timestamp('date_rejected')->nullable();
            
            $table->bigInteger('purchased_by')->nullable();
            $table->timestamp('date_purchased')->nullable();
            
            $table->bigInteger('delivered_by')->nullable();
            $table->timestamp('date_delivered')->nullable();
            
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requisitions');
    }
};
