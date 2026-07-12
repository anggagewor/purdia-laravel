<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('price_lists', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->index();
            $table->string('name');
            $table->string('slug');
            $table->string('type')->default('selling');
            $table->string('currency', 3)->default('IDR');
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['tenant_id', 'slug']);
        });

        Schema::create('price_list_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('price_list_id')->index();
            $table->unsignedBigInteger('product_id')->index();
            $table->unsignedBigInteger('variant_id')->nullable()->index();
            $table->unsignedBigInteger('branch_id')->nullable()->index();
            $table->unsignedInteger('min_qty')->nullable();
            $table->decimal('price', 20, 4);
            $table->string('currency', 3)->nullable();
            $table->timestamps();

            $table->index(['price_list_id', 'product_id', 'branch_id']);
        });

        Schema::create('discounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->index();
            $table->string('name');
            $table->string('code')->nullable();
            $table->string('type')->default('percentage');
            $table->decimal('value', 20, 4);
            $table->decimal('min_order_amount', 20, 4)->nullable();
            $table->decimal('max_discount_amount', 20, 4)->nullable();
            $table->unsignedInteger('usage_limit')->nullable();
            $table->unsignedInteger('usage_count')->default(0);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'code']);
        });

        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->index();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('scope')->default('global');
            $table->string('discount_type')->default('percentage');
            $table->decimal('discount_value', 20, 4);
            $table->unsignedInteger('min_qty')->nullable();
            $table->decimal('min_order_amount', 20, 4)->nullable();
            $table->decimal('max_discount_amount', 20, 4)->nullable();
            $table->unsignedInteger('priority')->default(0);
            $table->boolean('is_combinable')->default(false);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('promotion_rules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('promotion_id')->index();
            $table->string('entity_type');
            $table->string('entity_id');
            $table->timestamps();

            $table->index(['entity_type', 'entity_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promotion_rules');
        Schema::dropIfExists('promotions');
        Schema::dropIfExists('discounts');
        Schema::dropIfExists('price_list_items');
        Schema::dropIfExists('price_lists');
    }
};
