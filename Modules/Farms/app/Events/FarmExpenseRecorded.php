<?php

namespace Modules\Farms\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Farms\Models\FarmExpense;

class FarmExpenseRecorded
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly FarmExpense $farmExpense,
    ) {}
}