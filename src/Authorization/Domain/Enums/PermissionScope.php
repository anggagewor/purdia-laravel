<?php

namespace Purdia\Authorization\Domain\Enums;

enum PermissionScope: string
{
    case Page = 'page';
    case Component = 'component';
    case Action = 'action';
    case Api = 'api';
}
