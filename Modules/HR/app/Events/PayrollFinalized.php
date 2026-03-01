<?php

namespace Modules\HR\Events;

use Illuminate\Queue\SerializesModels;
use Modules\HR\Models\PayrollRun;

class PayrollFinalized
{
    use SerializesModels;

    public PayrollRun $payrollRun;

    public function __construct(PayrollRun $payrollRun)
    {
        $this->payrollRun = $payrollRun;
    }
}
