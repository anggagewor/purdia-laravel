<?php

namespace Purdia\Authorization\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Purdia\Authorization\Domain\Enums\PermissionScope;

class Permission extends Model
{
    protected $fillable = [
        'name',
        'description',
        'scope',
    ];

    protected function casts(): array
    {
        return [
            'scope' => PermissionScope::class,
        ];
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_permission');
    }
}
