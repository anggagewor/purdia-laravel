<?php

namespace Purdia\Identity\Application\Actions;

use Illuminate\Support\Facades\DB;
use Purdia\Identity\Application\DTOs\UpdateUserDTO;
use Purdia\Identity\Domain\Models\User;
use Purdia\Tenant\Application\Context\TenantContext;
use Purdia\Tenant\Domain\Models\BranchUser;
use Purdia\Tenant\Domain\Models\TenantUser;

class UpdateUserAction
{
    public function execute(string $userId, UpdateUserDTO $dto): User
    {
        $user = User::findOrFail($userId);

        return DB::transaction(function () use ($user, $dto) {
            $data = [
                'name' => $dto->name,
                'email' => $dto->email,
            ];

            if ($dto->password) {
                $data['password'] = $dto->password;
            }

            $user->update($data);

            // Update tenant role
            if ($dto->roleId && TenantContext::isResolved()) {
                $tenantUser = TenantUser::where('tenant_id', TenantContext::tenantId())
                    ->where('user_id', $user->id)
                    ->first();

                if ($tenantUser) {
                    $tenantUser->update(['role_id' => $dto->roleId]);
                } else {
                    $tenantUser = TenantUser::create([
                        'tenant_id' => TenantContext::tenantId(),
                        'user_id' => $user->id,
                        'role_id' => $dto->roleId,
                        'is_active' => true,
                    ]);
                }

                // Sync branch access
                if ($dto->branchIds !== null) {
                    BranchUser::where('tenant_user_id', $tenantUser->id)->delete();

                    foreach ($dto->branchIds as $branchId) {
                        BranchUser::create([
                            'tenant_user_id' => $tenantUser->id,
                            'branch_id' => $branchId,
                        ]);
                    }
                }
            }

            return $user->fresh();
        });
    }
}
