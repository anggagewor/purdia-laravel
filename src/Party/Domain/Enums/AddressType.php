<?php

namespace Purdia\Party\Domain\Enums;

enum AddressType: string
{
    case Billing = 'billing';
    case Shipping = 'shipping';
    case Office = 'office';
    case Warehouse = 'warehouse';
    case Home = 'home';
    case Other = 'other';
}
