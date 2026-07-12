<?php

namespace Purdia\Config\Application\Actions;

use Purdia\Config\Application\Exceptions\ConfigNotFoundException;
use Purdia\Config\Domain\Contracts\ConfigRepository;

class DeleteConfigAction
{
    public function __construct(
        private readonly ConfigRepository $configs,
    ) {}

    public function execute(string $group, string $key): void
    {
        if (! $this->configs->has($group, $key)) {
            throw new ConfigNotFoundException($group, $key);
        }

        $this->configs->delete($group, $key);
    }
}
