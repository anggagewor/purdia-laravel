<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('iso2', 2)->unique();
            $table->string('iso3', 3)->unique();
            $table->string('numeric_code', 3)->nullable();
            $table->string('phone_code', 10)->nullable();
            $table->string('capital')->nullable();
            $table->string('region')->nullable();
            $table->string('subregion')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('country_id')->index();
            $table->string('name');
            $table->string('code', 3);
            $table->string('symbol', 10);
            $table->unsignedTinyInteger('decimal_places')->default(2);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('currencies');
        Schema::dropIfExists('countries');
    }
};
