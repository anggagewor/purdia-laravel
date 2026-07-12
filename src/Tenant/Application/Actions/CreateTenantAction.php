<?php

namespace Purdia\Tenant\Application\Actions;

use Purdia\Tenant\Application\DTOs\CreateTenantDTO;
use Purdia\Tenant\Application\Exceptions\TenantSlugAlreadyExistsException;
use Purdia\Tenant\Domain\Models\Tenant;

class CreateTenantAction
{
    public function execute(CreateTenantDTO $dto): Tenant
    {
        if (Tenant::where('slug', $dto->slug)->exists()) {
            throw new TenantSlugAlreadyExistsException($dto->slug);
        }

        return Tenant::create([
            'name' => $dto->name,
            'slug' => $dto->slug,
            'currency' => $dto->currency,
            'locale' => $dto->locale,
            'timezone' => $dto->timezone,
            'settings' => $dto->settings,
        ]);
    }
}
