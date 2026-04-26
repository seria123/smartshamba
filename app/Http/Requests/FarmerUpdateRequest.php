<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FarmerUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name' => 'sometimes|string|max:255',
            'last_name' => 'sometimes|string|max:255',
            'phone' => 'nullable|string|max:20',
            'national_id' => 'nullable|string|max:50',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'address' => 'nullable|string|max:500',
            'village' => 'nullable|string|max:255',
            'ward' => 'nullable|string|max:255',
            'district' => 'nullable|string|max:255',
            'region' => 'nullable|string|max:255',
            'latitude' => 'nullable|between:-90,90',
            'longitude' => 'nullable|between:-180,180',
            'farm_size_hectares' => 'nullable|numeric|min:0',
            'farm_type' => 'nullable|in:smallholder,medium,large',
            'crop_history' => 'nullable|array',
            'farming_methods' => 'nullable|array',
            'notes' => 'nullable|string',
            'user.email' => 'sometimes|email|unique:users,email,'.$this->user()->id,
        ];
    }

    public function messages(): array
    {
        return [
            'user.email.unique' => 'This email is already in use',
            'farm_type.in' => 'Farm type must be smallholder, medium, or large',
            'latitude.between' => 'Latitude must be between -90 and 90',
            'longitude.between' => 'Longitude must be between -180 and 180',
        ];
    }
}
