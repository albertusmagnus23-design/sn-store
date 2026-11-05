<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Laravolt\Indonesia\Models\City;
use Laravolt\Indonesia\Models\District;
use Laravolt\Indonesia\Models\Province;

class CheckoutRegionController extends Controller
{
    public function cities(string $provinceCode): JsonResponse
    {
        $province = Province::query()->where('code', $provinceCode)->firstOrFail();

        return response()->json([
            'data' => $province->cities()->orderBy('name')->get(['code', 'name']),
        ]);
    }

    public function districts(string $cityCode): JsonResponse
    {
        $city = City::query()->where('code', $cityCode)->firstOrFail();

        return response()->json([
            'data' => $city->districts()->orderBy('name')->get(['code', 'name']),
        ]);
    }

    public function villages(string $districtCode): JsonResponse
    {
        $district = District::query()->where('code', $districtCode)->firstOrFail();

        return response()->json([
            'data' => $district->villages()->orderBy('name')->get(['code', 'name']),
        ]);
    }
}
