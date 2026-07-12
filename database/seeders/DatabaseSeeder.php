<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Purdia\Reference\Infrastructure\Database\Seeders\CountrySeeder;
use Purdia\Reference\Infrastructure\Database\Seeders\LookupSeeder;
use Purdia\Reference\Infrastructure\Database\Seeders\UnitSeeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            CountrySeeder::class,
            UnitSeeder::class,
            LookupSeeder::class,
        ]);
    }
}
