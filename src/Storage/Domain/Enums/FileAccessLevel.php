<?php

namespace Purdia\Storage\Domain\Enums;

enum FileAccessLevel: string
{
    case ReadOnly = 'read_only';
    case ReadWrite = 'read_write';
    case FullControl = 'full_control';
}
