<?php

namespace Purdia\Config\Application\Actions;

use Illuminate\Support\Collection;
use Purdia\Config\Application\DTOs\SetConfigDTO;
use Purdia\Config\Domain\Contracts\ConfigRepository;

class BulkSetConfigAction
{
    public function __construct(
        private readonly ConfigRepository $configs,
    ) {}

    /**
     * @param  SetConfigDTO[]  $items
     */
    public function execute(array $items): Collection
    {
        $results = collect();

        foreach ($items as $dto) {
            $results->push(
                $this->configs->set($dto->group, $dto->key, $dto->value, $dto->type)
            );
        }

        return $results;
    }
}
