<?php

namespace Purdia\Document\Domain\Enums;

enum ResetFrequency: string
{
    case Never = 'never';
    case Daily = 'daily';
    case Monthly = 'monthly';
    case Yearly = 'yearly';
}
