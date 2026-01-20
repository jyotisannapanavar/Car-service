<?php

namespace App\Services;

use App\Models\Branch;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class BranchService
{
    public function getAll(int $organizationId, int $perPage = 15): LengthAwarePaginator
    {
        return Branch::where('organization_id', $organizationId)
            ->orderBy('name')
            ->paginate($perPage);
    }

    public function getAllWithoutPagination(int $organizationId): Collection
    {
        return Branch::where('organization_id', $organizationId)
            ->orderBy('name')
            ->get();
    }

    public function findById(int $id): ?Branch
    {
        return Branch::find($id);
    }

    public function findByIdAndOrganization(int $id, int $organizationId): ?Branch
    {
        return Branch::where('id', $id)
            ->where('organization_id', $organizationId)
            ->first();
    }

    public function create(array $data): Branch
    {
        return Branch::create($data);
    }

    public function update(Branch $branch, array $data): Branch
    {
        $branch->update($data);

        return $branch->fresh();
    }

    public function delete(Branch $branch): bool
    {
        return $branch->delete();
    }
}
