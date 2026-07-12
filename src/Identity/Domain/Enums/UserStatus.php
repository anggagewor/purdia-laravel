<?php

namespace Purdia\Identity\Domain\Enums;

enum UserStatus: string
{
    case Active = 'active';
    case Inactive = 'inactive';
    case Suspended = 'suspended';
}
