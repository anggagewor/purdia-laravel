<?php

namespace Purdia\Catalog\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Purdia\Catalog\Domain\Enums\AttributeType;
use Purdia\Shared\Traits\BelongsToTenant;

class AttributeDefinition extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'name',
        'slug',
        'type',
        'options',
        'is_required',
        'is_filterable',
    ];

    protected function casts(): array
    {
        return [
            'type' => AttributeType::class,
            'options' => 'array',
            'is_required' => 'boolean',
            'is_filterable' => 'boolean',
        ];
    }
}
