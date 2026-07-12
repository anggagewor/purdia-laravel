<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('parties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('type');
            $table->string('display_name');
            $table->string('code')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->string('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'type']);
            $table->unique(['tenant_id', 'code']);
        });

        Schema::create('persons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('party_id')->constrained('parties')->cascadeOnDelete();
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->date('birth_date')->nullable();
            $table->string('gender')->nullable();
            $table->string('religion')->nullable();
            $table->string('blood_type')->nullable();
            $table->string('marital_status')->nullable();
            $table->string('national_id')->nullable();
            $table->timestamps();
        });

        Schema::create('organizations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('party_id')->constrained('parties')->cascadeOnDelete();
            $table->string('legal_name');
            $table->string('tax_number')->nullable();
            $table->string('npwp')->nullable();
            $table->string('nib')->nullable();
            $table->string('industry')->nullable();
            $table->string('website')->nullable();
            $table->timestamps();
        });

        Schema::create('party_contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('party_id')->constrained('parties')->cascadeOnDelete();
            $table->string('type');
            $table->string('label')->nullable();
            $table->string('value');
            $table->boolean('is_primary')->default(false);
            $table->timestamps();

            $table->index(['party_id', 'type']);
        });

        Schema::create('party_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('party_id')->constrained('parties')->cascadeOnDelete();
            $table->string('type');
            $table->string('label')->nullable();
            $table->string('line_1');
            $table->string('line_2')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('country_code', 2)->nullable();
            $table->boolean('is_primary')->default(false);
            $table->timestamps();

            $table->index(['party_id', 'type']);
        });

        Schema::create('party_roles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('party_id')->constrained('parties')->cascadeOnDelete();
            $table->string('role');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['party_id', 'role']);
            $table->index('role');
        });

        Schema::create('party_relationships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('party_a_id')->constrained('parties')->cascadeOnDelete();
            $table->foreignId('party_b_id')->constrained('parties')->cascadeOnDelete();
            $table->string('type');
            $table->string('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['party_a_id', 'type']);
            $table->index(['party_b_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('party_relationships');
        Schema::dropIfExists('party_roles');
        Schema::dropIfExists('party_addresses');
        Schema::dropIfExists('party_contacts');
        Schema::dropIfExists('organizations');
        Schema::dropIfExists('persons');
        Schema::dropIfExists('parties');
    }
};
