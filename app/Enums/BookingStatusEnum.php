<?php

namespace App\Enums;

/**
 * Enum OdooSettingKeyEnum
 *
 * Defines Odoo settings key enumeration
 */
enum BookingStatusEnum: string
{
    case CONFIRMED = 'confirmed';
    case CANCELLED = 'cancelled';
}
