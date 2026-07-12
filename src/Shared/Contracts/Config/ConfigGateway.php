<?php

namespace Purdia\Shared\Contracts\Config;

interface ConfigGateway
{
    /**
     * Get a config value by group and key.
     *
     * Usage: $config->get('pos', 'tax_rate', 11)
     * For global configs: $config->get('general', 'app.name', 'Purdia')
     */
    public function get(string $group, string $key, mixed $default = null): mixed;

    /**
     * Check if a config key exists.
     */
    public function has(string $group, string $key): bool;
}
