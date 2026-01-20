<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicle_service_pricing', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->onDelete('cascade');
            $table->foreignId('service_id')->constrained()->onDelete('cascade');
            $table->foreignId('vehicle_type_id')->constrained()->onDelete('cascade');
            $table->foreignId('vehicle_brand_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('vehicle_model_id')->nullable()->constrained()->onDelete('cascade');
            $table->decimal('price', 10, 2);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index('branch_id');
            $table->index('service_id');
            $table->index('vehicle_type_id');
            $table->index('vehicle_brand_id');
            $table->index('vehicle_model_id');

            $table->unique(
                ['branch_id', 'service_id', 'vehicle_type_id', 'vehicle_brand_id', 'vehicle_model_id'],
                'unique_pricing_rule'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_service_pricing');
    }
};
