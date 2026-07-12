<?php

namespace Purdia\Identity\Application\Actions;

use Illuminate\Support\Facades\DB;
use Purdia\Identity\Application\DTOs\CreateUserDTO;
use Purdia\Identity\Application\Exceptions\UserAlreadyExistsException;
use Purdia\Identity\Domain\Contracts\UserRepository;
use Purdia\Identity\Domain\Models\User;
use Purdia\Tenant\Application\Context\TenantContext;
use Purdia\Tenant\Domain\Models\BranchUser;
use Purdia\Tenant\Domain\Models\TenantUser;

class CreateUserAction
{
    public function __construct(
        private readonly UserRepository $users,
    ) {}

    public function execute(CreateUserDTO $dto): User
    {
        if ($this->users->existsByEmail($dto->email)) {
            throw new UserAlreadyExistsException($dto->email);
        }

        return DB::transaction(function () use ($dto) {
            $user = User::create([
                'name' => $dto->name,
                'email' => $dto->email,
                'password' => $dto->password,
            ]);

            // Assign to current tenant with role
            if ($dto->roleId && TenantContext::isResolved()) {
                $tenantUser = TenantUser::create([
                    'tenant_id' => TenantContext::tenantId(),
                    'user_id' => $user->id,
                    'role_id' => $dto->roleId,
                    'is_active' => true,
                ]);

                // Assign branch access
                if ($dto->branchIds) {
                    foreach ($dto->branchIds as $branchId) {
                        BranchUser::create([
                            'tenant_user_id' => $tenantUser->id,
                            'branch_id' => $branchId,
                        ]);
                    }
                }
            }

            return $user;
        });
    }
}
