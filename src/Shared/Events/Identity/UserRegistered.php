<?php

namespace Purdia\Shared\Events\Identity;

use Illuminate\Foundation\Events\Dispatchable;

class UserRegistered
{
    use Dispatchable;

    public function __construct(
        public readonly string $userId,
        public readonly string $email,
        public readonly string $name,
    ) {}
}
