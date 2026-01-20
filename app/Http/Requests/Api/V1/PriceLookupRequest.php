<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class PriceLookupRequest extends FormRequest
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
        ];
    }
}
