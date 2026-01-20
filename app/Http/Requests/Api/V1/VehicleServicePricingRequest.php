<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class VehicleServicePricingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'branch_id' => ['required', 'exists:branches,id'],
            'service_id' => ['required', 'exists:services,id'],
            'vehicle_type_id' => ['required', 'exists:vehicle_types,id'],
            'vehicle_brand_id' => ['nullable', 'exists:vehicle_brands,id'],
            'vehicle_model_id' => ['nullable', 'exists:vehicle_models,id'],
            'price' => ['required', 'numeric', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'branch_id.required' => 'Branch is required',
            'service_id.required' => 'Service is required',
            'vehicle_type_id.required' => 'Vehicle type is required',
            'price.required' => 'Price is required',
            'price.min' => 'Price must be a positive number',
        ];
    }
}
