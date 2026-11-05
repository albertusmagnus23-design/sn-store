<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Laravolt\Indonesia\Models\City;
use Laravolt\Indonesia\Models\District;
use Laravolt\Indonesia\Models\Province;
use Laravolt\Indonesia\Models\Village;

class CheckoutShippingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'province_code' => [
                'required',
                'string',
                Rule::exists((new Province)->getTable(), 'code'),
            ],
            'city_code' => [
                'required',
                'string',
                Rule::exists((new City)->getTable(), 'code')
                    ->where('province_code', $this->input('province_code')),
            ],
            'district_code' => [
                'required',
                'string',
                Rule::exists((new District)->getTable(), 'code')
                    ->where('city_code', $this->input('city_code')),
            ],
            'village_code' => [
                'required',
                'string',
                Rule::exists((new Village)->getTable(), 'code')
                    ->where('district_code', $this->input('district_code')),
            ],
            'postal_code' => ['required', 'string', 'regex:/^\d{5}$/'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'province_code' => 'provinsi',
            'city_code' => 'kabupaten/kota',
            'district_code' => 'kecamatan',
            'village_code' => 'desa/kelurahan',
            'postal_code' => 'kode pos',
        ];
    }
}
