<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('original_name');
            $table->string('path');
            $table->string('disk');
            $table->string('mime_type');
            $table->unsignedBigInteger('size');
            $table->string('extension', 20);
            $table->string('visibility')->default('private');
            $table->string('module')->index();
            $table->string('entity_type')->nullable();
            $table->string('entity_id')->nullable();
            $table->string('uploaded_by')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['entity_type', 'entity_id']);
            $table->index(['module', 'entity_type']);
        });

        Schema::create('file_accesses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('file_id')->index();
            $table->string('accessor_type');
            $table->string('accessor_id');
            $table->string('access_level')->default('read_only');
            $table->timestamps();

            $table->unique(['file_id', 'accessor_type', 'accessor_id'], 'file_access_unique');
            $table->index(['accessor_type', 'accessor_id']);
        });

        Schema::create('storage_rules', function (Blueprint $table) {
            $table->id();
            $table->string('mime_pattern')->nullable();
            $table->string('extension_pattern')->nullable();
            $table->string('disk');
            $table->string('path_prefix')->nullable();
            $table->unsignedBigInteger('max_size')->nullable();
            $table->string('visibility_default')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('storage_rules');
        Schema::dropIfExists('file_accesses');
        Schema::dropIfExists('files');
    }
};
