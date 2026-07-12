<?php

namespace Purdia\Document\Domain\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentRevision extends Model
{
    protected $fillable = [
        'document_type',
        'document_id',
        'revision_number',
        'data',
        'reason',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'revision_number' => 'integer',
            'data' => 'array',
        ];
    }
}
