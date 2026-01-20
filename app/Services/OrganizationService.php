<?php

namespace App\Services;

use App\Models\Organization;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class OrganizationService
{
    public function getAll(int $perPage = 15): LengthAwarePaginator
    {
        return Organization::orderBy('name')->paginate($perPage);
    }

    public function getAllWithoutPagination(): Collection
    {
        return Organization::orderBy('name')->get();
    }

    public function findById(int $id): ?Organization
    {
        return Organization::find($id);
    }

    public function create(array $data): Organization
    {
        return Organization::create($data);
    }

    public function update(Organization $organization, array $data): Organization
    {
        $organization->update($data);

        return $organization->fresh();
    }

    public function delete(Organization $organization): bool
    {
        return $organization->delete();
    }
}
