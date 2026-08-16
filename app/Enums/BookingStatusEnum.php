<?php

namespace App\Enums;

/**
 * Booking Status Enum
 */
enum BookingStatusEnum: string
{
    case CONFIRMED = 'confirmed';
    case CANCELLED = 'cancelled';
}
