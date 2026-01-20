<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function getAll(int $organizationId, ?int $branchId = null, int $perPage = 15): LengthAwarePaginator
    {
        $query = User::where('organization_id', $organizationId);

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        return $query->orderBy('name')->paginate($perPage);
    }

    public function findById(int $id): ?User
    {
        return User::find($id);
    }

    public function findByIdAndOrganization(int $id, int $organizationId): ?User
    {
        return User::where('id', $id)
            ->where('organization_id', $organizationId)
            ->first();
    }

    public function create(array $data): User
    {
        $data['password'] = Hash::make($data['password']);

        return User::create($data);
    }

    public function update(User $user, array $data): User
    {
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);

        return $user->fresh();
    }

    public function delete(User $user): bool
    {
        return $user->delete();
    }
}
