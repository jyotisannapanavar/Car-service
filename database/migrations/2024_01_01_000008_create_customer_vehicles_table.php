<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('customer_vehicles', function (Blueprint $table) {
            $table->id();

            // Organization & Branch
            $table->foreignId('org_id')
                ->nullable()
                ->constrained('organizations')
                ->nullOnDelete();

            $table->foreignId('branch_id')
                ->nullable()
                ->constrained('branches')
                ->nullOnDelete();

            // Customer
            $table->foreignId('customer_id')
                ->nullable()
                ->constrained('customers')
                ->nullOnDelete();

            // Vehicle hierarchy
            $table->foreignId('vehicle_type_id')
                ->nullable()
                ->constrained('vehicle_types')
                ->nullOnDelete();

            $table->foreignId('vehicle_brand_id')
                ->nullable()
                ->constrained('vehicle_brands')
                ->nullOnDelete();

            $table->foreignId('vehicle_model_id')
                ->nullable()
                ->constrained('vehicle_models')
                ->nullOnDelete();

            // Vehicle details
            $table->string('registration_number')->nullable();
            $table->string('color')->nullable();
            $table->year('year')->nullable();
            $table->text('notes')->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('org_id');
            $table->index('branch_id');
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
