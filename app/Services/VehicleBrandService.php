<?php

namespace App\Services;

use App\Models\VehicleBrand;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class VehicleBrandService
{
    public function getAll(?int $vehicleTypeId = null, int $perPage = 15): LengthAwarePaginator
    {
        $query = VehicleBrand::query();

        if ($vehicleTypeId) {
            $query->where('vehicle_type_id', $vehicleTypeId);
        }

        return $query->orderBy('name')->paginate($perPage);
    }

    public function getAllByType(int $vehicleTypeId): Collection
    {
        return VehicleBrand::where('vehicle_type_id', $vehicleTypeId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    public function findById(int $id): ?VehicleBrand
    {
        return VehicleBrand::find($id);
    }

    public function create(array $data): VehicleBrand
    {
        return VehicleBrand::create($data);
    }

    public function update(VehicleBrand $vehicleBrand, array $data): VehicleBrand
    {
        $vehicleBrand->update($data);

        return $vehicleBrand->fresh();
    }

    public function delete(VehicleBrand $vehicleBrand): bool
    {
        return $vehicleBrand->delete();
    }
}
