<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BranchController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\CustomerVehicleController;
use App\Http\Controllers\Api\OrganizationController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\VehicleBrandController;
use App\Http\Controllers\Api\VehicleModelController;
use App\Http\Controllers\Api\VehicleServicePricingController;
use App\Http\Controllers\Api\VehicleTypeController;
use Illuminate\Support\Facades\Route;


Route::post('/login', [\App\Http\Controllers\Api\AuthController::class, 'login']);
Route::post('/register', [\App\Http\Controllers\Api\AuthController::class, 'register']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    Route::get('organizations', [OrganizationController::class, 'index']);
    Route::post('organizations', [OrganizationController::class, 'store']);
    Route::get('organizations/{id}', [OrganizationController::class, 'show']);
    Route::put('organizations/{id}', [OrganizationController::class, 'update']);
    Route::delete('organizations/{id}', [OrganizationController::class, 'destroy']);

    Route::get('branches', [BranchController::class, 'index']);
    Route::post('branches', [BranchController::class, 'store']);
    Route::get('branches/{branch}', [BranchController::class, 'show']);
    Route::put('branches/{branch}', [BranchController::class, 'update']);
    Route::delete('branches/{branch}', [BranchController::class, 'destroy']);

    Route::get('users', [UserController::class, 'index']);
    Route::post('users', [UserController::class, 'store']);
    Route::get('users/{user}', [UserController::class, 'show']);
    Route::put('users/{user}', [UserController::class, 'update']);
    Route::delete('users/{user}', [UserController::class, 'destroy']);

    Route::get('vehicle-types-list', [VehicleTypeController::class, 'list']);
    Route::get('vehicle-types', [VehicleTypeController::class, 'index']);
    Route::post('vehicle-types', [VehicleTypeController::class, 'store']);
    Route::get('vehicle-types/{id}', [VehicleTypeController::class, 'show']);
    Route::put('vehicle-types/{vehicleType}', [VehicleTypeController::class, 'update']);
    Route::delete('vehicle-types/{vehicleType}', [VehicleTypeController::class, 'destroy']);

    Route::get('vehicle-brands/by-type/{vehicleTypeId}', [VehicleBrandController::class, 'listByType']);
    Route::get('vehicle-brands', [VehicleBrandController::class, 'index']);
    Route::post('vehicle-brands', [VehicleBrandController::class, 'store']);
    Route::get('vehicle-brands/{vehicleBrand}', [VehicleBrandController::class, 'show']);
    Route::put('vehicle-brands/{vehicleBrand}', [VehicleBrandController::class, 'update']);
    Route::delete('vehicle-brands/{vehicleBrand}', [VehicleBrandController::class, 'destroy']);

    Route::get('vehicle-models/by-brand/{vehicleBrandId}', [VehicleModelController::class, 'listByBrand']);
    Route::get('vehicle-models', [VehicleModelController::class, 'index']);
    Route::post('vehicle-models', [VehicleModelController::class, 'store']);
    Route::get('vehicle-models/{vehicleModel}', [VehicleModelController::class, 'show']);
    Route::put('vehicle-models/{vehicleModel}', [VehicleModelController::class, 'update']);
    Route::delete('vehicle-models/{vehicleModel}', [VehicleModelController::class, 'destroy']);

    Route::get('services/by-branch/{branchId}', [ServiceController::class, 'listByBranch']);
    Route::get('services', [ServiceController::class, 'index']);
    Route::post('services', [ServiceController::class, 'store']);
    Route::get('services/{service}', [ServiceController::class, 'show']);
    Route::put('services/{service}', [ServiceController::class, 'update']);
    Route::delete('services/{service}', [ServiceController::class, 'destroy']);

    Route::get('customers/search', [CustomerController::class, 'searchByPhone']);
    Route::get('customers', [CustomerController::class, 'index']);
    Route::post('customers', [CustomerController::class, 'store']);
    Route::get('customers/{customer}', [CustomerController::class, 'show']);
    Route::put('customers/{customer}', [CustomerController::class, 'update']);
    Route::delete('customers/{customer}', [CustomerController::class, 'destroy']);

    Route::get('customers/{customer}/vehicles', [CustomerVehicleController::class, 'index']);
    Route::post('customer-vehicles', [CustomerVehicleController::class, 'store']);
    Route::get('customers/{customer}/vehicles/{vehicle}', [CustomerVehicleController::class, 'show']);
    Route::put('customers/{customer}/vehicles/{vehicle}', [CustomerVehicleController::class, 'update']);
    Route::delete('customers/{customer}/vehicles/{vehicle}', [CustomerVehicleController::class, 'destroy']);

    Route::get('pricing/lookup', [VehicleServicePricingController::class, 'lookup']);
    Route::get('pricing/by-service/{serviceId}', [VehicleServicePricingController::class, 'getByService']);
    Route::get('pricing', [VehicleServicePricingController::class, 'index']);
    Route::post('pricing', [VehicleServicePricingController::class, 'store']);
    Route::get('pricing/{pricing}', [VehicleServicePricingController::class, 'show']);
    Route::put('pricing/{pricing}', [VehicleServicePricingController::class, 'update']);
    Route::delete('pricing/{pricing}', [VehicleServicePricingController::class, 'destroy']);

});

