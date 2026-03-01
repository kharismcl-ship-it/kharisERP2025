<?php

namespace Modules\Finance\Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;
use Modules\Finance\Models\Account;

class ChartOfAccountsSeeder extends Seeder
{
    /**
     * Standard chart of accounts seeded for every company.
     * Covers Assets, Liabilities, Equity, Income, and Expenses.
     */
    public function run(): void
    {
        $accounts = [
            // ── ASSETS ──────────────────────────────────────────────
            ['code' => '1000', 'name' => 'Assets',                       'type' => 'asset',     'parent_code' => null],
            ['code' => '1100', 'name' => 'Current Assets',               'type' => 'asset',     'parent_code' => '1000'],
            ['code' => '1110', 'name' => 'Cash on Hand',                  'type' => 'asset',     'parent_code' => '1100'],
            ['code' => '1120', 'name' => 'Bank Account',                  'type' => 'asset',     'parent_code' => '1100'],
            ['code' => '1130', 'name' => 'Mobile Money (MoMo)',           'type' => 'asset',     'parent_code' => '1100'],
            ['code' => '1200', 'name' => 'Accounts Receivable',          'type' => 'asset',     'parent_code' => '1100'],
            ['code' => '1210', 'name' => 'Trade Receivables',            'type' => 'asset',     'parent_code' => '1200'],
            ['code' => '1300', 'name' => 'Inventory',                    'type' => 'asset',     'parent_code' => '1100'],
            ['code' => '1400', 'name' => 'Prepaid Expenses',             'type' => 'asset',     'parent_code' => '1100'],
            ['code' => '1500', 'name' => 'Non-Current Assets',          'type' => 'asset',     'parent_code' => '1000'],
            ['code' => '1510', 'name' => 'Property & Equipment',         'type' => 'asset',     'parent_code' => '1500'],
            ['code' => '1520', 'name' => 'Accumulated Depreciation',     'type' => 'asset',     'parent_code' => '1500'],

            // ── LIABILITIES ─────────────────────────────────────────
            ['code' => '2000', 'name' => 'Liabilities',                  'type' => 'liability', 'parent_code' => null],
            ['code' => '2100', 'name' => 'Current Liabilities',          'type' => 'liability', 'parent_code' => '2000'],
            ['code' => '2110', 'name' => 'Accounts Payable',             'type' => 'liability', 'parent_code' => '2100'],
            ['code' => '2120', 'name' => 'Accrued Liabilities',          'type' => 'liability', 'parent_code' => '2100'],
            ['code' => '2130', 'name' => 'VAT Payable',                  'type' => 'liability', 'parent_code' => '2100'],
            ['code' => '2140', 'name' => 'Income Tax Payable',           'type' => 'liability', 'parent_code' => '2100'],
            ['code' => '2150', 'name' => 'Pension Payable (SSNIT)',      'type' => 'liability', 'parent_code' => '2100'],
            ['code' => '2160', 'name' => 'Withholding Tax Payable',      'type' => 'liability', 'parent_code' => '2100'],
            ['code' => '2170', 'name' => 'Customer Deposits',            'type' => 'liability', 'parent_code' => '2100'],
            ['code' => '2200', 'name' => 'Non-Current Liabilities',     'type' => 'liability', 'parent_code' => '2000'],
            ['code' => '2210', 'name' => 'Long-Term Loans',              'type' => 'liability', 'parent_code' => '2200'],

            // ── EQUITY ──────────────────────────────────────────────
            ['code' => '3000', 'name' => 'Equity',                       'type' => 'equity',    'parent_code' => null],
            ['code' => '3100', 'name' => 'Owner Capital',                'type' => 'equity',    'parent_code' => '3000'],
            ['code' => '3200', 'name' => 'Retained Earnings',            'type' => 'equity',    'parent_code' => '3000'],
            ['code' => '3300', 'name' => 'Current Year Earnings',        'type' => 'equity',    'parent_code' => '3000'],

            // ── INCOME ──────────────────────────────────────────────
            ['code' => '4000', 'name' => 'Revenue',                      'type' => 'income',    'parent_code' => null],
            ['code' => '4100', 'name' => 'Hostel Revenue',               'type' => 'income',    'parent_code' => '4000'],
            ['code' => '4110', 'name' => 'Room Rental Income',           'type' => 'income',    'parent_code' => '4100'],
            ['code' => '4120', 'name' => 'Utility Charges Income',       'type' => 'income',    'parent_code' => '4100'],
            ['code' => '4200', 'name' => 'Farm Revenue',                 'type' => 'income',    'parent_code' => '4000'],
            ['code' => '4300', 'name' => 'Construction Revenue',        'type' => 'income',    'parent_code' => '4000'],
            ['code' => '4400', 'name' => 'Manufacturing Revenue',       'type' => 'income',    'parent_code' => '4000'],
            ['code' => '4900', 'name' => 'Other Income',                 'type' => 'income',    'parent_code' => '4000'],

            // ── EXPENSES ────────────────────────────────────────────
            ['code' => '5000', 'name' => 'Expenses',                     'type' => 'expense',   'parent_code' => null],
            ['code' => '5100', 'name' => 'Cost of Sales',               'type' => 'expense',   'parent_code' => '5000'],
            ['code' => '5200', 'name' => 'Payroll & Staff Costs',       'type' => 'expense',   'parent_code' => '5000'],
            ['code' => '5210', 'name' => 'Salaries & Wages',            'type' => 'expense',   'parent_code' => '5200'],
            ['code' => '5220', 'name' => 'SSNIT Employer Contribution', 'type' => 'expense',   'parent_code' => '5200'],
            ['code' => '5230', 'name' => 'Staff Benefits',              'type' => 'expense',   'parent_code' => '5200'],
            ['code' => '5300', 'name' => 'Operating Expenses',         'type' => 'expense',   'parent_code' => '5000'],
            ['code' => '5310', 'name' => 'Rent & Lease',               'type' => 'expense',   'parent_code' => '5300'],
            ['code' => '5320', 'name' => 'Utilities',                   'type' => 'expense',   'parent_code' => '5300'],
            ['code' => '5330', 'name' => 'Office Supplies',            'type' => 'expense',   'parent_code' => '5300'],
            ['code' => '5340', 'name' => 'Maintenance & Repairs',      'type' => 'expense',   'parent_code' => '5300'],
            ['code' => '5400', 'name' => 'Fleet Expenses',             'type' => 'expense',   'parent_code' => '5000'],
            ['code' => '5410', 'name' => 'Fuel Expense',               'type' => 'expense',   'parent_code' => '5400'],
            ['code' => '5420', 'name' => 'Vehicle Maintenance',        'type' => 'expense',   'parent_code' => '5400'],
            ['code' => '5500', 'name' => 'Procurement / Purchases',    'type' => 'expense',   'parent_code' => '5000'],
            ['code' => '5600', 'name' => 'Depreciation',               'type' => 'expense',   'parent_code' => '5000'],
            ['code' => '5700', 'name' => 'Finance Costs',              'type' => 'expense',   'parent_code' => '5000'],
            ['code' => '5710', 'name' => 'Bank Charges',               'type' => 'expense',   'parent_code' => '5700'],
            ['code' => '5720', 'name' => 'Interest Expense',           'type' => 'expense',   'parent_code' => '5700'],
            ['code' => '5900', 'name' => 'Other Expenses',             'type' => 'expense',   'parent_code' => '5000'],
        ];

        foreach (Company::all() as $company) {
            // Build a code→id map per company so children can reference parents
            $codeToId = [];

            foreach ($accounts as $data) {
                $parentId = $data['parent_code'] ? ($codeToId[$data['parent_code']] ?? null) : null;

                $account = Account::firstOrCreate(
                    ['company_id' => $company->id, 'code' => $data['code']],
                    [
                        'name'      => $data['name'],
                        'type'      => $data['type'],
                        'parent_id' => $parentId,
                    ]
                );

                $codeToId[$data['code']] = $account->id;
            }
        }
    }
}
