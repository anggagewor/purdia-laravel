<?php

namespace Purdia\Pricing\Domain\Enums;

enum PriceListType: string
{
    case Selling = 'selling';
    case Buying = 'buying';
}
