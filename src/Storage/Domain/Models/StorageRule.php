<?php

namespace Purdia\Storage\Domain\Models;

use Illuminate\Database\Eloquent\Model;

class StorageRule extends Model
{
    protected $fillable = [
        'mime_pattern',
        'extension_pattern',
        'disk',
        'path_prefix',
        'max_size',
        'visibility_default',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'max_size' => 'integer',
            'is_active' => 'boolean',
        ];
    }
}
