<?php

namespace App\Services;

use App\Models\VehicleModel;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class VehicleModelService
{
    public function getAll(?int $vehicleBrandId = null, int $perPage = 15): LengthAwarePaginator
    {
        $query = VehicleModel::query();

        if ($vehicleBrandId) {
            $query->where('vehicle_brand_id', $vehicleBrandId);
        }

        return $query->orderBy('name')->paginate($perPage);
    }

    public function getAllByBrand(int $vehicleBrandId): Collection
    {
        return VehicleModel::where('vehicle_brand_id', $vehicleBrandId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    public function findById(int $id): ?VehicleModel
    {
        return VehicleModel::find($id);
    }

    public function create(array $data): VehicleModel
    {
        return VehicleModel::create($data);
    }

    public function update(VehicleModel $vehicleModel, array $data): VehicleModel
    {
        $vehicleModel->update($data);

        return $vehicleModel->fresh();
    }

    public function delete(VehicleModel $vehicleModel): bool
    {
        return $vehicleModel->delete();
    }
}
