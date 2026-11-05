<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Laravolt\Indonesia\Models\City;
use Laravolt\Indonesia\Models\District;
use Laravolt\Indonesia\Models\Province;
use Laravolt\Indonesia\Models\Village;

class CheckoutRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'min:8', 'max:30', 'regex:/^\+?[0-9][0-9\s()\-]{6,27}[0-9]$/'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($this->user()->id),
            ],
            'street_address' => ['required', 'string', 'max:1500'],
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
            'shipping_quote_id' => ['required', 'uuid'],
            'shipping_option' => ['required', 'string', 'size:64'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'street_address' => 'alamat jalan',
            'province_code' => 'provinsi',
            'city_code' => 'kabupaten/kota',
            'district_code' => 'kecamatan',
            'village_code' => 'desa/kelurahan',
            'postal_code' => 'kode pos',
            'shipping_quote_id' => 'kutipan ongkir',
            'shipping_option' => 'layanan pengiriman',
        ];
    }
}
