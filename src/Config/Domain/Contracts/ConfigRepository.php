<?php

namespace Purdia\Config\Domain\Contracts;

use Illuminate\Support\Collection;
use Purdia\Config\Domain\Models\Config;

interface ConfigRepository
{
    /**
     * Get a config value by group and key.
     * Key uses dot notation: "app.name", "mail.from_address"
     */
    public function get(string $group, string $key, mixed $default = null): mixed;

    /**
     * Set a config value.
     */
    public function set(string $group, string $key, mixed $value, string $type = 'string'): Config;

    /**
     * Get all configs for a group.
     */
    public function getGroup(string $group): Collection;

    /**
     * Get all available groups.
     */
    public function getGroups(): Collection;

    /**
     * Delete a config entry.
     */
    public function delete(string $group, string $key): bool;

    /**
     * Check if a config key exists.
     */
    public function has(string $group, string $key): bool;
}
