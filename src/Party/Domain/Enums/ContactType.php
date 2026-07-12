<?php

namespace Purdia\Party\Domain\Enums;

enum ContactType: string
{
    case Email = 'email';
    case Phone = 'phone';
    case Mobile = 'mobile';
    case Fax = 'fax';
    case Website = 'website';
    case WhatsApp = 'whatsapp';
    case Other = 'other';
}
