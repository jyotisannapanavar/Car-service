<?php

namespace App\Services;

use App\Models\CustomerVehicle;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class CustomerVehicleService
{
    public function getAll(int $customerId, int $perPage = 15): LengthAwarePaginator
    {
        return CustomerVehicle::where('customer_id', $customerId)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function getAllByCustomer(int $customerId): Collection
    {
        return CustomerVehicle::where('customer_id', $customerId)
            ->where('is_active', true)
            ->with(['vehicleType', 'vehicleBrand', 'vehicleModel'])
            ->get();
    }

    public function findById(int $id): ?CustomerVehicle
    {
        return CustomerVehicle::find($id);
    }

    public function findByIdAndCustomer(int $id, int $customerId): ?CustomerVehicle
    {
        return CustomerVehicle::where('id', $id)
            ->where('customer_id', $customerId)
            ->first();
    }

    public function create(array $data): CustomerVehicle
    {
        return CustomerVehicle::create($data);
    }

    public function update(CustomerVehicle $customerVehicle, array $data): CustomerVehicle
    {
        $customerVehicle->update($data);

        return $customerVehicle->fresh();
    }

    public function delete(CustomerVehicle $customerVehicle): bool
    {
        return $customerVehicle->delete();
    }
}
