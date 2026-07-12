<?php

namespace Purdia\Config\Infrastructure\Gateway;

use Purdia\Config\Domain\Contracts\ConfigRepository;
use Purdia\Shared\Contracts\Config\ConfigGateway;

class ConfigGatewayImpl implements ConfigGateway
{
    public function __construct(
        private readonly ConfigRepository $configs,
    ) {}

    public function get(string $group, string $key, mixed $default = null): mixed
    {
        return $this->configs->get($group, $key, $default);
    }

    public function has(string $group, string $key): bool
    {
        return $this->configs->has($group, $key);
    }
}
