<?php

namespace Purdia\Party\Domain\Enums;

enum PartyType: string
{
    case Person = 'person';
    case Organization = 'organization';
}
