<?php

namespace Purdia\Pricing\Domain\Enums;

enum PromotionScope: string
{
    case Product = 'product';
    case Category = 'category';
    case Brand = 'brand';
    case Cart = 'cart';
    case Global = 'global';
}
