<?php

namespace Modules\Finance\Interfaces;

interface ExpensableInterface
{
    /**
     * Get the company ID for the expense
     */
    public function getCompanyId(): int;

    /**
     * Get the total amount for the expense
     */
    public function getExpenseAmount(): float;

    /**
     * Get the description for the expense
     */
    public function getExpenseDescription(): string;

    /**
     * Get the expense type/category
     */
    public function getExpenseType(): string;
}
