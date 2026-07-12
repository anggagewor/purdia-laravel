<?php

namespace Purdia\Storage\Domain\Enums;

enum FileVisibility: string
{
    case Public = 'public';
    case Private = 'private';
    case Restricted = 'restricted';
}
