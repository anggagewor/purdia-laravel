<?php

namespace Purdia\Storage\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Purdia\Storage\Domain\Enums\FileVisibility;

class File extends Model
{
    protected $fillable = [
        'name',
        'original_name',
        'path',
        'disk',
        'mime_type',
        'size',
        'extension',
        'visibility',
        'module',
        'entity_type',
        'entity_id',
        'uploaded_by',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'size' => 'integer',
            'visibility' => FileVisibility::class,
            'metadata' => 'array',
        ];
    }

    public function accesses(): HasMany
    {
        return $this->hasMany(FileAccess::class);
    }

    public function isPublic(): bool
    {
        return $this->visibility === FileVisibility::Public;
    }

    public function isPrivate(): bool
    {
        return $this->visibility === FileVisibility::Private;
    }

    public function isRestricted(): bool
    {
        return $this->visibility === FileVisibility::Restricted;
    }
}
