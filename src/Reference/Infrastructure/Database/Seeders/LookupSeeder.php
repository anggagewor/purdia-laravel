<?php

namespace Purdia\Reference\Infrastructure\Database\Seeders;

use Illuminate\Database\Seeder;
use Purdia\Reference\Domain\Models\Language;
use Purdia\Reference\Domain\Models\LookupItem;
use Purdia\Reference\Domain\Models\LookupType;
use Purdia\Reference\Domain\Models\TaxCategory;
use Purdia\Reference\Domain\Models\Timezone;

class LookupSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedTimezones();
        $this->seedLanguages();
        $this->seedTaxCategories();
        $this->seedLookups();
    }

    private function seedTimezones(): void
    {
        $data = json_decode(
            file_get_contents(__DIR__.'/data/timezones.json'),
            true
        );

        foreach ($data as $item) {
            Timezone::updateOrCreate(
                ['code' => $item['code']],
                [
                    'name' => $item['name'],
                    'offset' => $item['offset'],
                    'utc_offset' => $item['utc_offset'],
                ],
            );
        }
    }

    private function seedLanguages(): void
    {
        $data = json_decode(
            file_get_contents(__DIR__.'/data/languages.json'),
            true
        );

        foreach ($data as $item) {
            Language::updateOrCreate(
                ['code' => $item['code']],
                [
                    'name' => $item['name'],
                    'native_name' => $item['native_name'] ?? null,
                ],
            );
        }
    }

    private function seedTaxCategories(): void
    {
        $data = json_decode(
            file_get_contents(__DIR__.'/data/tax_categories.json'),
            true
        );

        foreach ($data as $item) {
            TaxCategory::updateOrCreate(
                ['slug' => $item['slug']],
                [
                    'name' => $item['name'],
                    'rate' => $item['rate'],
                    'description' => $item['description'] ?? null,
                ],
            );
        }
    }

    private function seedLookups(): void
    {
        $data = json_decode(
            file_get_contents(__DIR__.'/data/lookups.json'),
            true
        );

        foreach ($data as $typeData) {
            $type = LookupType::updateOrCreate(
                ['slug' => $typeData['slug']],
                [
                    'name' => $typeData['name'],
                    'description' => $typeData['description'] ?? null,
                ],
            );

            foreach ($typeData['items'] as $itemData) {
                LookupItem::updateOrCreate(
                    ['type_id' => $type->id, 'slug' => $itemData['slug']],
                    [
                        'name' => $itemData['name'],
                        'sort_order' => $itemData['sort_order'] ?? 0,
                    ],
                );
            }
        }
    }
}
