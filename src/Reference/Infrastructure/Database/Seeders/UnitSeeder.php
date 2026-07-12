<?php

namespace Purdia\Reference\Infrastructure\Database\Seeders;

use Illuminate\Database\Seeder;
use Purdia\Reference\Domain\Models\Unit;
use Purdia\Reference\Domain\Models\UnitCategory;
use Purdia\Reference\Domain\Models\UnitConversion;

class UnitSeeder extends Seeder
{
    public function run(): void
    {
        $data = $this->getData();

        foreach ($data as $categoryData) {
            $category = UnitCategory::updateOrCreate(
                ['slug' => $categoryData['slug']],
                ['name' => $categoryData['name']],
            );

            $unitMap = [];

            foreach ($categoryData['units'] as $unitData) {
                $unit = Unit::updateOrCreate(
                    ['category_id' => $category->id, 'symbol' => $unitData['symbol']],
                    [
                        'name' => $unitData['name'],
                        'is_base' => $unitData['is_base'] ?? false,
                    ],
                );
                $unitMap[$unitData['symbol']] = $unit;
            }

            foreach ($categoryData['conversions'] as $conversion) {
                $from = $unitMap[$conversion['from']];
                $to = $unitMap[$conversion['to']];

                UnitConversion::updateOrCreate(
                    ['from_unit_id' => $from->id, 'to_unit_id' => $to->id],
                    ['factor' => $conversion['factor']],
                );

                // Reverse conversion
                UnitConversion::updateOrCreate(
                    ['from_unit_id' => $to->id, 'to_unit_id' => $from->id],
                    ['factor' => 1 / $conversion['factor']],
                );
            }
        }
    }

    private function getData(): array
    {
        return json_decode(
            file_get_contents(__DIR__.'/data/units.json'),
            true
        );
    }
}
