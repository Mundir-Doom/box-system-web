<?php

use App\Enums\SmsSendStatus;

return array (
    SmsSendStatus::PARCEL_CREATE                 => 'تم إنشاء الشحنة',
    SmsSendStatus::DELIVERED_CANCEL_CUSTOMER     => 'إلغاء التسليم من قبل العميل',
    SmsSendStatus::DELIVERED_CANCEL_MERCHANT     => 'إلغاء التسليم من قبل التاجر',

);
