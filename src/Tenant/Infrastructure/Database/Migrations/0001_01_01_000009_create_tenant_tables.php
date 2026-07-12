<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('currency', 3)->default('IDR');
            $table->string('locale', 10)->default('id');
            $table->string('timezone')->default('Asia/Jakarta');
            $table->string('logo')->nullable();
            $table->string('favicon')->nullable();
            $table->json('settings')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->index();
            $table->unsignedBigInteger('parent_branch_id')->nullable()->index();
            $table->string('name');
            $table->string('code', 20);
            $table->string('type')->default('store');
            $table->text('address')->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('timezone')->nullable();
            $table->json('settings')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['tenant_id', 'code']);
        });

        Schema::create('tenant_users', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->index();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('role_id')->index();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['tenant_id', 'user_id', 'role_id']);
        });

        Schema::create('branch_users', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_user_id')->index();
            $table->unsignedBigInteger('branch_id')->index();
            $table->timestamps();

            $table->unique(['tenant_user_id', 'branch_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('branch_users');
        Schema::dropIfExists('tenant_users');
        Schema::dropIfExists('branches');
        Schema::dropIfExists('tenants');
    }
};
