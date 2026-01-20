<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('vehicle_brands', function (Blueprint $table) {
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

            // Vehicle Type
            $table->foreignId('vehicle_type_id')
                ->constrained('vehicle_types')
                ->cascadeOnDelete();

            $table->string('name');
            $table->string('logo')->nullable();
            $table->boolean('is_active')->default(true);

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('org_id');
            $table->index('branch_id');
            $table->index('vehicle_type_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_brands');
    }
};
