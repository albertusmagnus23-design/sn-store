<?php

namespace App\Services;

use Laravolt\Indonesia\Models\Village;

class CheckoutAddress
{
    /**
     * @param  array{street_address: string, postal_code: string, village_code: string}  $address
     */
    public function format(array $address): string
    {
        $village = Village::query()
            ->with(['district.city.province'])
            ->where('code', $address['village_code'])
            ->firstOrFail();

        return implode(', ', [
            $address['street_address'],
            $village->district->city->province->name,
            $village->district->city->name,
            $village->district->name,
            $village->name,
            'Kode Pos '.$address['postal_code'],
        ]);
    }
}
