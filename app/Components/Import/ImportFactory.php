<?php


namespace App\Components\Import;


use App\Models\Account;
use App\Models\User;

class ImportFactory
{

    /**
     * @param $type
     * @param Account $account
     * @param User $user
     * @return CompanyImporter|CustomerImporter|DealImporter|ExpenseImporter|InvoiceImporter|LeadImporter|PaymentImporter|ProductImporter
     * @throws CsvImporterException
     */
    public function loadImporter($type, Account $account, User $user)
    {
        switch ($type) {
            case 'customer':
                return new CustomerImporter($account, $user);
                break;

            case 'deal':
                return new DealImporter($account, $user);
                break;

            case 'lead':
                return new LeadImporter($account, $user);
                break;

            case 'invoice':
                return new InvoiceImporter($account, $user);
                break;

            case 'product':
                return new ProductImporter($account, $user);
                break;

            case 'expense':
                return new ExpenseImporter($account, $user);
                break;

            case 'company':
                return new CompanyImporter($account, $user);
                break;
            case 'payment':
                return new PaymentImporter($account, $user);
                break;
        }
    }
}
