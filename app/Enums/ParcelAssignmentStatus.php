<?php

namespace App\Enums;

interface ParcelAssignmentStatus
{
    const COLLECTED         = 1;
    const ASSIGNED          = 2;
    const OUT_FOR_DELIVERY  = 3;
    const DELIVERED         = 4;
}
