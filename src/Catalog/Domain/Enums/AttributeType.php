<?php

namespace Purdia\Catalog\Domain\Enums;

enum AttributeType: string
{
    case Text = 'text';
    case Number = 'number';
    case Boolean = 'boolean';
    case Select = 'select';
    case MultiSelect = 'multi_select';
    case Color = 'color';
    case Date = 'date';
}
