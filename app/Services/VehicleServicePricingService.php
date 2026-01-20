<?php

namespace App\Services;

use App\Models\VehicleServicePricing;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class VehicleServicePricingService
{
    public function getAll(int $branchId, ?int $serviceId = null, int $perPage = 15): LengthAwarePaginator
    {
        $query = VehicleServicePricing::where('branch_id', $branchId);

        if ($serviceId) {
            $query->where('service_id', $serviceId);
        }

        return $query->with(['service', 'vehicleType', 'vehicleBrand', 'vehicleModel'])
            ->orderBy('service_id')
            ->paginate($perPage);
    }

    public function findById(int $id): ?VehicleServicePricing
    {
        return VehicleServicePricing::find($id);
    }

    public function findByIdAndBranch(int $id, int $branchId): ?VehicleServicePricing
    {
        return VehicleServicePricing::where('id', $id)
            ->where('branch_id', $branchId)
            ->first();
    }

    public function lookupPrice(
        int $branchId,
        int $serviceId,
        int $vehicleTypeId,
        ?int $vehicleBrandId = null,
        ?int $vehicleModelId = null
    ): ?VehicleServicePricing {
        $query = VehicleServicePricing::where('branch_id', $branchId)
            ->where('service_id', $serviceId)
            ->where('vehicle_type_id', $vehicleTypeId)
            ->where('is_active', true);

        if ($vehicleModelId) {
            $exactMatch = (clone $query)
                ->where('vehicle_brand_id', $vehicleBrandId)
                ->where('vehicle_model_id', $vehicleModelId)
                ->first();

            if ($exactMatch) {
                return $exactMatch;
            }
        }

        if ($vehicleBrandId) {
            $brandMatch = (clone $query)
                ->where('vehicle_brand_id', $vehicleBrandId)
                ->whereNull('vehicle_model_id')
                ->first();

            if ($brandMatch) {
                return $brandMatch;
            }
        }

        return $query
            ->whereNull('vehicle_brand_id')
            ->whereNull('vehicle_model_id')
            ->first();
    }

    public function checkDuplicatePricing(
        int $branchId,
        int $serviceId,
        int $vehicleTypeId,
        ?int $vehicleBrandId = null,
        ?int $vehicleModelId = null,
        ?int $excludeId = null
    ): bool {
        $query = VehicleServicePricing::where('branch_id', $branchId)
            ->where('service_id', $serviceId)
            ->where('vehicle_type_id', $vehicleTypeId);

        if ($vehicleBrandId) {
            $query->where('vehicle_brand_id', $vehicleBrandId);
        } else {
            $query->whereNull('vehicle_brand_id');
        }

        if ($vehicleModelId) {
            $query->where('vehicle_model_id', $vehicleModelId);
        } else {
            $query->whereNull('vehicle_model_id');
        }

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    public function create(array $data): VehicleServicePricing
    {
        return VehicleServicePricing::create($data);
    }

    public function update(VehicleServicePricing $pricing, array $data): VehicleServicePricing
    {
        $pricing->update($data);

        return $pricing->fresh();
    }

    public function delete(VehicleServicePricing $pricing): bool
    {
        return $pricing->delete();
    }

    public function getPricingByService(int $branchId, int $serviceId): Collection
    {
        return VehicleServicePricing::where('branch_id', $branchId)
            ->where('service_id', $serviceId)
            ->where('is_active', true)
            ->with(['vehicleType', 'vehicleBrand', 'vehicleModel'])
            ->get();
    }
}
