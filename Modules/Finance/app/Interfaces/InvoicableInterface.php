<?php

namespace Modules\Finance\Interfaces;

interface InvoicableInterface
{
    /**
     * Get the customer name for the invoice
     */
    public function getCustomerName(): string;

    /**
     * Get the customer type for the invoice
     */
    public function getCustomerType(): string;

    /**
     * Get the company ID for the invoice
     */
    public function getCompanyId(): int;

    /**
     * Get the total amount for the invoice
     */
    public function getTotalAmount(): float;

    /**
     * Get line items for the invoice
     */
    public function getInvoiceLineItems(): array;
}
