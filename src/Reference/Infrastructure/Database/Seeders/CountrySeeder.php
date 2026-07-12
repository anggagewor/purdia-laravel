<?php

namespace Purdia\Reference\Infrastructure\Database\Seeders;

use Illuminate\Database\Seeder;
use Purdia\Reference\Domain\Models\Country;
use Purdia\Reference\Domain\Models\Currency;

class CountrySeeder extends Seeder
{
    public function run(): void
    {
        $countries = $this->getData();

        foreach ($countries as $data) {
            $country = Country::updateOrCreate(
                ['iso2' => $data['iso2']],
                [
                    'name' => $data['name'],
                    'iso3' => $data['iso3'],
                    'numeric_code' => $data['numeric_code'] ?? null,
                    'phone_code' => $data['phone_code'] ?? null,
                    'capital' => $data['capital'] ?? null,
                    'region' => $data['region'] ?? null,
                    'subregion' => $data['subregion'] ?? null,
                ]
            );

            if (isset($data['currency'])) {
                Currency::updateOrCreate(
                    ['country_id' => $country->id, 'code' => $data['currency']['code']],
                    [
                        'name' => $data['currency']['name'],
                        'symbol' => $data['currency']['symbol'],
                        'decimal_places' => $data['currency']['decimal_places'] ?? 2,
                    ]
                );
            }
        }
    }

    private function getData(): array
    {
        return json_decode(
            file_get_contents(__DIR__.'/data/countries.json'),
            true
        );
    }
}
