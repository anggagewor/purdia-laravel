<?php

namespace Purdia\Pricing\Domain\Enums;

enum DiscountType: string
{
    case Percentage = 'percentage';
    case Fixed = 'fixed';
}
