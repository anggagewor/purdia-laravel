<?php

namespace Purdia\Config\Application\Actions;

use Purdia\Config\Application\DTOs\SetConfigDTO;
use Purdia\Config\Domain\Contracts\ConfigRepository;
use Purdia\Config\Domain\Models\Config;

class SetConfigAction
{
    public function __construct(
        private readonly ConfigRepository $configs,
    ) {}

    public function execute(SetConfigDTO $dto): Config
    {
        return $this->configs->set($dto->group, $dto->key, $dto->value, $dto->type);
    }
}
