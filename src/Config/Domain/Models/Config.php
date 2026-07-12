<?php

namespace Purdia\Config\Domain\Models;

use Illuminate\Database\Eloquent\Model;

class Config extends Model
{
    protected $table = 'configs';

    protected $fillable = [
        'group',
        'key',
        'value',
        'type',
    ];

    protected function casts(): array
    {
        return [
            'value' => 'string',
        ];
    }

    /**
     * Get the typed value based on the 'type' column.
     */
    public function getTypedValueAttribute(): mixed
    {
        return match ($this->type) {
            'boolean' => filter_var($this->value, FILTER_VALIDATE_BOOLEAN),
            'integer' => (int) $this->value,
            'float' => (float) $this->value,
            'json' => json_decode($this->value, true),
            'array' => json_decode($this->value, true),
            default => $this->value,
        };
    }
}
