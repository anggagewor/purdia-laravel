<?php

namespace Purdia\Storage\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Purdia\Storage\Domain\Enums\FileAccessLevel;

class FileAccess extends Model
{
    protected $table = 'file_accesses';

    protected $fillable = [
        'file_id',
        'accessor_type',
        'accessor_id',
        'access_level',
    ];

    protected function casts(): array
    {
        return [
            'access_level' => FileAccessLevel::class,
        ];
    }

    public function file(): BelongsTo
    {
        return $this->belongsTo(File::class);
    }
}
