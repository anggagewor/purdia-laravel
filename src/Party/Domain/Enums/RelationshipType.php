<?php

namespace Purdia\Party\Domain\Enums;

enum RelationshipType: string
{
    case WorksFor = 'works_for';
    case Owns = 'owns';
    case ReportsTo = 'reports_to';
    case BelongsTo = 'belongs_to';
    case PartnerOf = 'partner_of';
    case SubsidiaryOf = 'subsidiary_of';
    case Other = 'other';
}
