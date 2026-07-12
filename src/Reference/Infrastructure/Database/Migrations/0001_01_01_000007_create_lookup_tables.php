<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('timezones', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->string('offset', 10);
            $table->decimal('utc_offset', 5, 2);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('languages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 5)->unique();
            $table->string('native_name')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('tax_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->decimal('rate', 8, 4)->default(0);
            $table->string('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('lookup_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('description')->nullable();
            $table->timestamps();
        });

        Schema::create('lookup_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('type_id')->index();
            $table->string('name');
            $table->string('slug');
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['type_id', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lookup_items');
        Schema::dropIfExists('lookup_types');
        Schema::dropIfExists('tax_categories');
        Schema::dropIfExists('languages');
        Schema::dropIfExists('timezones');
    }
};
