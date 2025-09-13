<?php

use App\Enums\ApprovalStatus;

return [
    ApprovalStatus::REJECT    => 'مرفوض',
    ApprovalStatus::APPROVED  => 'موافق عليه',
    ApprovalStatus::PENDING   => 'قيد الانتظار',
    ApprovalStatus::PROCESSED => 'قيد المعالجة',

];
