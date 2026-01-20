<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->foreignId('vehicle_type_id')->constrained()->onDelete('cascade');
            $table->foreignId('vehicle_brand_id')->constrained()->onDelete('cascade');
            $table->foreignId('vehicle_model_id')->constrained()->onDelete('cascade');
            $table->string('registration_number')->nullable();
            $table->string('color')->nullable();
            $table->year('year')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index('customer_id');
            $table->index('vehicle_type_id');
            $table->index('vehicle_brand_id');
            $table->index('vehicle_model_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_vehicles');
    }
};
