<?php

namespace Purdia\Config\Domain\Enums;

enum ConfigType: string
{
    case String = 'string';
    case Boolean = 'boolean';
    case Integer = 'integer';
    case Float = 'float';
    case Json = 'json';
    case Array = 'array';
}
