<?php

namespace App\Services;

use App\Models\VehicleType;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class VehicleTypeService
{
    public function getAll(int $perPage = 15): LengthAwarePaginator
    {
        return VehicleType::orderBy('name')->paginate($perPage);
    }

    public function getAllWithoutPagination(): Collection
    {
        return VehicleType::where('is_active', true)->orderBy('name')->get();
    }

    public function findById(int $id): ?VehicleType
    {
        return VehicleType::find($id);
    }

    public function create(array $data): VehicleType
    {
        return VehicleType::create($data);
    }

    public function update(VehicleType $vehicleType, array $data): VehicleType
    {
        $vehicleType->update($data);

        return $vehicleType->fresh();
    }

    public function delete(VehicleType $vehicleType): bool
    {
        return $vehicleType->delete();
    }
}
