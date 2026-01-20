<?php

namespace App\Services;

use App\Models\Customer;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CustomerService
{
    public function getAll(int $organizationId, ?int $branchId = null, ?string $search = null, int $perPage = 15): LengthAwarePaginator
    {
        $query = Customer::where('organization_id', $organizationId);

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        return $query->orderBy('name')->paginate($perPage);
    }

    public function findById(int $id): ?Customer
    {
        return Customer::find($id);
    }

    public function findByIdAndOrganization(int $id, int $organizationId): ?Customer
    {
        return Customer::where('id', $id)
            ->where('organization_id', $organizationId)
            ->first();
    }

    public function findByPhone(string $phone, int $organizationId): ?Customer
    {
        return Customer::where('phone', $phone)
            ->where('organization_id', $organizationId)
            ->first();
    }

    public function create(array $data): Customer
    {
        return Customer::create($data);
    }

    public function update(Customer $customer, array $data): Customer
    {
        $customer->update($data);

        return $customer->fresh();
    }

    public function delete(Customer $customer): bool
    {
        return $customer->delete();
    }
}
