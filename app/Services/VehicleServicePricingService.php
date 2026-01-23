<?php

namespace App\Services;

use App\Models\Branch;
use App\Models\VehicleServicePricing;
use Exception;

class VehicleServicePricingService
{
    public function index(int $orgId, ?int $branchId = null, ?int $serviceId = null, ?string $search = null, int $perPage = 15): array
    {
        try {
            if (!$branchId) {
                return [
                    'success' => false,
                    'message' => 'Branch ID is required',
                    'status' => 400,
                ];
            }

            $branch = Branch::where('id', $branchId)
                ->where('org_id', $orgId)
                ->first();

            if (!$branch) {
                return [
                    'success' => false,
                    'message' => 'Branch does not belong to your organization',
                    'status' => 403,
                ];
            }

            $query = VehicleServicePricing::where('branch_id', $branchId);

            if ($serviceId) {
                $query->where('service_id', $serviceId);
            }

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('price', 'like', "%{$search}%")
                        ->orWhereHas('service', function ($sq) use ($search) {
                            $sq->where('name', 'like', "%{$search}%");
                        })
                        ->orWhereHas('vehicleType', function ($vq) use ($search) {
                            $vq->where('name', 'like', "%{$search}%");
                        })
                        ->orWhereHas('vehicleBrand', function ($vq) use ($search) {
                            $vq->where('name', 'like', "%{$search}%");
                        })
                        ->orWhereHas('vehicleModel', function ($vq) use ($search) {
                            $vq->where('name', 'like', "%{$search}%");
                        });
                });
            }

            $pricing = $query->with(['service', 'vehicleType', 'vehicleBrand', 'vehicleModel'])
                ->orderBy('service_id')
                ->paginate($perPage);

            return [
                'success' => true,
                'message' => 'Pricing retrieved successfully',
                'data' => $pricing->items(),
                'pagination' => [
                    'current_page' => $pricing->currentPage(),
                    'total_pages' => $pricing->lastPage(),
                    'per_page' => $pricing->perPage(),
                    'total' => $pricing->total(),
                    'next_page_url' => $pricing->nextPageUrl(),
                    'prev_page_url' => $pricing->previousPageUrl(),
                ],
                'status' => 200,
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to retrieve pricing: ' . $e->getMessage(),
                'status' => 500,
            ];
        }
    }

    public function store(array $data, int $orgId, int $branchId): array
    {
        try {
            $branch = Branch::where('id', $branchId)
                ->where('org_id', $orgId)
                ->first();

            if (!$branch) {
                return [
                    'success' => false,
                    'message' => 'Branch does not belong to your organization',
                    'status' => 403,
                ];
            }

            // 🔥 Force org_id & branch_id
            $data['branch_id'] = $branchId;
            $data['org_id'] = $orgId;

            $isDuplicate = $this->checkDuplicatePricing(
                $branchId,
                $data['service_id'],
                $data['vehicle_type_id'],
                $data['vehicle_brand_id'] ?? null,
                $data['vehicle_model_id'] ?? null
            );

            if ($isDuplicate) {
                return [
                    'success' => false,
                    'message' => 'A pricing rule with these parameters already exists',
                    'status' => 409,
                ];
            }

            $pricing = VehicleServicePricing::create($data);
            $pricing->load(['service', 'vehicleType', 'vehicleBrand', 'vehicleModel']);

            return [
                'success' => true,
                'message' => 'Pricing created successfully',
                'data' => $pricing,
                'status' => 201,
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to create pricing: ' . $e->getMessage(),
                'status' => 500,
            ];
        }
    }


    public function show(int $id, int $orgId): array
    {
        try {
            $pricing = VehicleServicePricing::find($id);

            if (!$pricing) {
                return [
                    'success' => false,
                    'message' => 'Pricing not found',
                    'status' => 404,
                ];
            }

            $branch = Branch::where('id', $pricing->branch_id)
                ->where('org_id', $orgId)
                ->first();

            if (!$branch) {
                return [
                    'success' => false,
                    'message' => 'Pricing does not belong to your organization',
                    'status' => 403,
                ];
            }

            $pricing->load(['branch', 'service', 'vehicleType', 'vehicleBrand', 'vehicleModel']);

            return [
                'success' => true,
                'message' => 'Pricing retrieved successfully',
                'data' => $pricing,
                'status' => 200,
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to retrieve pricing: ' . $e->getMessage(),
                'status' => 500,
            ];
        }
    }

    public function update(int $id, array $data, int $orgId): array
    {
        try {
            $pricing = VehicleServicePricing::find($id);

            if (!$pricing) {
                return [
                    'success' => false,
                    'message' => 'Pricing not found',
                    'status' => 404,
                ];
            }

            $branch = Branch::where('id', $pricing->branch_id)
                ->where('org_id', $orgId)
                ->first();

            if (!$branch) {
                return [
                    'success' => false,
                    'message' => 'Pricing does not belong to your organization',
                    'status' => 403,
                ];
            }

            $isDuplicate = $this->checkDuplicatePricing(
                $data['branch_id'],
                $data['service_id'],
                $data['vehicle_type_id'],
                $data['vehicle_brand_id'] ?? null,
                $data['vehicle_model_id'] ?? null,
                $id
            );

            if ($isDuplicate) {
                return [
                    'success' => false,
                    'message' => 'A pricing rule with these parameters already exists',
                    'status' => 409,
                ];
            }

            $pricing->update($data);
            $pricing->load(['service', 'vehicleType', 'vehicleBrand', 'vehicleModel']);

            return [
                'success' => true,
                'message' => 'Pricing updated successfully',
                'data' => $pricing->fresh(),
                'status' => 200,
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to update pricing: ' . $e->getMessage(),
                'status' => 500,
            ];
        }
    }

    public function destroy(int $id, int $orgId): array
    {
        try {
            $pricing = VehicleServicePricing::find($id);

            if (!$pricing) {
                return [
                    'success' => false,
                    'message' => 'Pricing not found',
                    'status' => 404,
                ];
            }

            $branch = Branch::where('id', $pricing->branch_id)
                ->where('org_id', $orgId)
                ->first();

            if (!$branch) {
                return [
                    'success' => false,
                    'message' => 'Pricing does not belong to your organization',
                    'status' => 403,
                ];
            }

            $pricing->delete();

            return [
                'success' => true,
                'message' => 'Pricing deleted successfully',
                'data' => null,
                'status' => 200,
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to delete pricing: ' . $e->getMessage(),
                'status' => 500,
            ];
        }
    }

    public function lookup(array $data, int $orgId): array
    {
        try {
            $branch = Branch::where('id', $data['branch_id'])
                ->where('org_id', $orgId)
                ->first();

            if (!$branch) {
                return [
                    'success' => false,
                    'message' => 'Branch does not belong to your organization',
                    'status' => 403,
                ];
            }

            $pricing = $this->lookupPrice(
                $data['branch_id'],
                $data['service_id'],
                $data['vehicle_type_id'],
                $data['vehicle_brand_id'] ?? null,
                $data['vehicle_model_id'] ?? null
            );

            if (!$pricing) {
                return [
                    'success' => false,
                    'message' => 'No pricing found for the specified parameters',
                    'status' => 404,
                ];
            }

            $pricing->load(['service', 'vehicleType', 'vehicleBrand', 'vehicleModel']);

            return [
                'success' => true,
                'message' => 'Price found',
                'data' => [
                    'pricing' => $pricing,
                    'price' => $pricing->price,
                    'match_type' => $this->getMatchType($pricing, $data),
                ],
                'status' => 200,
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to lookup price: ' . $e->getMessage(),
                'status' => 500,
            ];
        }
    }

    public function getByService(int $serviceId, int $orgId, ?int $branchId = null): array
    {
        try {
            if (!$branchId) {
                return [
                    'success' => false,
                    'message' => 'Branch ID is required',
                    'status' => 400,
                ];
            }

            $branch = Branch::where('id', $branchId)
                ->where('org_id', $orgId)
                ->first();

            if (!$branch) {
                return [
                    'success' => false,
                    'message' => 'Branch does not belong to your organization',
                    'status' => 403,
                ];
            }

            $pricing = VehicleServicePricing::where('branch_id', $branchId)
                ->where('service_id', $serviceId)
                ->where('is_active', true)
                ->with(['vehicleType', 'vehicleBrand', 'vehicleModel'])
                ->get();

            return [
                'success' => true,
                'message' => 'Pricing retrieved successfully',
                'data' => $pricing,
                'status' => 200,
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to retrieve pricing: ' . $e->getMessage(),
                'status' => 500,
            ];
        }
    }

    private function lookupPrice(
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

    private function checkDuplicatePricing(
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

    private function getMatchType(VehicleServicePricing $pricing, array $data): string
    {
        if ($pricing->vehicle_model_id && $pricing->vehicle_model_id == ($data['vehicle_model_id'] ?? null)) {
            return 'exact_model';
        }

        if ($pricing->vehicle_brand_id && $pricing->vehicle_brand_id == ($data['vehicle_brand_id'] ?? null)) {
            return 'brand_level';
        }

        return 'type_level';
    }
}
