<?php

namespace Purdia\Config\Application\Actions;

use Purdia\Config\Domain\Contracts\ConfigRepository;

class GetConfigAction
{
    public function __construct(
        private readonly ConfigRepository $configs,
    ) {}

    public function execute(string $group, string $key, mixed $default = null): mixed
    {
        return $this->configs->get($group, $key, $default);
    }
}
