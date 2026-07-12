<?php

namespace Purdia\Authorization\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Purdia\Identity\Domain\Models\User;

class Role extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'role_permission');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_role');
    }
}
