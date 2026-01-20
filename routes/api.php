<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\BranchController;
use App\Http\Controllers\Api\V1\CustomerController;
use App\Http\Controllers\Api\V1\CustomerVehicleController;
use App\Http\Controllers\Api\V1\OrganizationController;
use App\Http\Controllers\Api\V1\ServiceController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\VehicleBrandController;
use App\Http\Controllers\Api\V1\VehicleModelController;
use App\Http\Controllers\Api\V1\VehicleServicePricingController;
use App\Http\Controllers\Api\V1\VehicleTypeController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);

        Route::middleware(['role:admin'])->group(function () {
            Route::apiResource('organizations', OrganizationController::class);
        });

        Route::middleware(['check.organization'])->group(function () {
            Route::apiResource('branches', BranchController::class);

            Route::middleware(['role:admin,branch_manager'])->group(function () {
                Route::apiResource('users', UserController::class);
            });

            Route::get('vehicle-types/list', [VehicleTypeController::class, 'list']);
            Route::apiResource('vehicle-types', VehicleTypeController::class);

            Route::get('vehicle-brands/by-type/{vehicleTypeId}', [VehicleBrandController::class, 'listByType']);
            Route::apiResource('vehicle-brands', VehicleBrandController::class);

            Route::get('vehicle-models/by-brand/{vehicleBrandId}', [VehicleModelController::class, 'listByBrand']);
            Route::apiResource('vehicle-models', VehicleModelController::class);

            Route::get('services/by-branch/{branchId}', [ServiceController::class, 'listByBranch']);
            Route::apiResource('services', ServiceController::class);

            Route::get('customers/search', [CustomerController::class, 'searchByPhone']);
            Route::apiResource('customers', CustomerController::class);

            Route::get('customers/{customer}/vehicles', [CustomerVehicleController::class, 'index']);
            Route::post('customer-vehicles', [CustomerVehicleController::class, 'store']);
            Route::get('customers/{customer}/vehicles/{vehicle}', [CustomerVehicleController::class, 'show']);
            Route::put('customers/{customer}/vehicles/{vehicle}', [CustomerVehicleController::class, 'update']);
            Route::delete('customers/{customer}/vehicles/{vehicle}', [CustomerVehicleController::class, 'destroy']);

            Route::get('pricing/lookup', [VehicleServicePricingController::class, 'lookup']);
            Route::get('pricing/by-service/{serviceId}', [VehicleServicePricingController::class, 'getByService']);
            Route::apiResource('pricing', VehicleServicePricingController::class);
        });
    });
});
