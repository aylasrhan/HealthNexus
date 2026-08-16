<?php

namespace App\Enums;

enum AppointmentStatus: int
{
    case Pending = 0;
    case Confirmed = 1;
    case Cancelled = 2;
}
