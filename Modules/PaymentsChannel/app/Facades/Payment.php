<?php

namespace Modules\PaymentsChannel\Facades;

use Illuminate\Support\Facades\Facade;

class Payment extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'payment';
    }

    /**
     * Get available payment methods for a company with optional filtering.
     */
    public static function getAvailablePaymentMethods(?int $companyId = null, array $filters = [])
    {
        return static::getFacadeRoot()->getAvailablePaymentMethods($companyId, $filters);
    }

    /**
     * Get payment methods grouped by provider with optional filtering.
     */
    public static function getGroupedPaymentMethods(?int $companyId = null, array $filters = [])
    {
        return static::getFacadeRoot()->getGroupedPaymentMethods($companyId, $filters);
    }
}
