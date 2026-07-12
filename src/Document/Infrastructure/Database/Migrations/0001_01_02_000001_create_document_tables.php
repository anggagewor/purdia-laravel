<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sequences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->string('type', 50);
            $table->string('prefix', 20);
            $table->string('format');
            $table->unsignedInteger('current_number')->default(0);
            $table->string('reset_frequency')->default('monthly');
            $table->timestamp('last_reset_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['tenant_id', 'branch_id', 'type']);
        });

        Schema::create('document_revisions', function (Blueprint $table) {
            $table->id();
            $table->string('document_type');
            $table->string('document_id');
            $table->unsignedInteger('revision_number');
            $table->json('data');
            $table->string('reason')->nullable();
            $table->string('created_by')->nullable();
            $table->timestamps();

            $table->index(['document_type', 'document_id']);
            $table->unique(['document_type', 'document_id', 'revision_number'], 'doc_revisions_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_revisions');
        Schema::dropIfExists('sequences');
    }
};
