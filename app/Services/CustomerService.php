<?php

namespace App\Services;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\Customer;
use Exception;

class CustomerService
{
    public function index(int $orgId, ?int $branchId = null, ?string $search = null, int $perPage = 15): array
    {
        try {
            $query = Customer::where('org_id', $orgId);

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

            $customers = $query->orderBy('name')->paginate($perPage);

            return [
                'success' => true,
                'message' => 'Customers retrieved successfully',
                'data' => $customers->items(),
                'pagination' => [
                    'current_page' => $customers->currentPage(),
                    'total_pages' => $customers->lastPage(),
                    'per_page' => $customers->perPage(),
                    'total' => $customers->total(),
                    'next_page_url' => $customers->nextPageUrl(),
                    'prev_page_url' => $customers->previousPageUrl(),
                ],
                'status' => 200,
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to retrieve customers: '.$e->getMessage(),
                'status' => 500,
            ];
        }
    }

 public function store(array $data, User $authUser): array
{
    DB::beginTransaction();

    try {
        // 🔐 FORCE org_id & branch_id FROM TOKEN
        $data['org_id'] = $authUser->org_id;
        $data['branch_id'] = $authUser->branch_id;

        /**
         * Generate default password
         */
        $defaultPassword = Str::random(10);

        /**
         * Create user
         */
        $user = User::create([
            'name'      => $data['name'],
            'email'     => $data['email'] ?? null,
            'phone'     => $data['phone'],
            'org_id'    => $authUser->org_id,
            'branch_id' => $authUser->branch_id,
            'password'  => Hash::make('password'),
            'role'      => 'customer',
            'is_active' => $data['is_active'] ?? true,
        ]);

        /**
         * Attach user_id to customer
         */
        $data['user_id'] = $user->id;

        /**
         * Create customer
         */
        $customer = Customer::create($data);

        DB::commit();

        return [
            'success' => true,
            'message' => 'Customer created successfully',
            'data' => [
                'customer' => $customer,
                'default_password' => $defaultPassword,
            ],
            'status' => 201,
        ];
    } catch (Exception $e) {
        DB::rollBack();

        return [
            'success' => false,
            'message' => 'Failed to create customer: ' . $e->getMessage(),
            'status' => 500,
        ];
    }
}



    public function show(int $id): array
    {
        try {
            $customer = Customer::where('id', $id)
                // ->where('org_id', $orgId)
                ->first();

            if (! $customer) {
                return [
                    'success' => false,
                    'message' => 'Customer not found',
                    'status' => 404,
                ];
            }

            $customer->load(['vehicles.vehicleType', 'vehicles.vehicleBrand', 'vehicles.vehicleModel']);

            return [
                'success' => true,
                'message' => 'Customer retrieved successfully',
                'data' => $customer,
                'status' => 200,
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to retrieve customer: '.$e->getMessage(),
                'status' => 500,
            ];
        }
    }

    public function update(int $id, int $orgId, array $data): array
    {
        try {
            $customer = Customer::where('id', $id)
                ->where('org_id', $orgId)
                ->first();

            if (! $customer) {
                return [
                    'success' => false,
                    'message' => 'Customer not found',
                    'status' => 404,
                ];
            }

            $customer->update($data);

            return [
                'success' => true,
                'message' => 'Customer updated successfully',
                'data' => $customer->fresh(),
                'status' => 200,
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to update customer: '.$e->getMessage(),
                'status' => 500,
            ];
        }
    }

    public function destroy(int $id, int $orgId): array
    {
        try {
            $customer = Customer::where('id', $id)
                ->where('org_id', $orgId)
                ->first();

            if (! $customer) {
                return [
                    'success' => false,
                    'message' => 'Customer not found',
                    'status' => 404,
                ];
            }

            $customer->delete();

            return [
                'success' => true,
                'message' => 'Customer deleted successfully',
                'data' => null,
                'status' => 200,
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to delete customer: '.$e->getMessage(),
                'status' => 500,
            ];
        }
    }

    public function searchByPhone(string $phone, int $orgId): array
    {
        try {
            if (empty($phone)) {
                return [
                    'success' => false,
                    'message' => 'Phone number is required',
                    'status' => 400,
                ];
            }

            $customer = Customer::where('phone', $phone)
                ->where('org_id', $orgId)
                ->first();

            if (! $customer) {
                return [
                    'success' => false,
                    'message' => 'Customer not found',
                    'status' => 404,
                ];
            }

            $customer->load(['vehicles.vehicleType', 'vehicles.vehicleBrand', 'vehicles.vehicleModel']);

            return [
                'success' => true,
                'message' => 'Customer found',
                'data' => $customer,
                'status' => 200,
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to search customer: '.$e->getMessage(),
                'status' => 500,
            ];
        }
    }
}
