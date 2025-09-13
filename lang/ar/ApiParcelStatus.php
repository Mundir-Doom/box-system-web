<?php

use App\Enums\ParcelStatus;

return [
    ParcelStatus::RETURN_TO_COURIER  => 'إرجاع إلى شركة التوصيل',
    ParcelStatus::PARTIAL_DELIVERED  => 'تسليم جزئي',
    ParcelStatus::DELIVERED          => 'تم التسليم'
];
