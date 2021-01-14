<?php

namespace App\Services\Account;

use App\Factory\Account\CloneAccountToAddressFactory;
use App\Factory\Account\CloneAccountToContactFactory;
use App\Factory\Account\CloneAccountToCustomerFactory;
use App\Factory\Account\CloneAccountToUserFactory;
use App\Models\Account;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class ConvertLead
 * @package App\Services\Task
 */
class ConvertAccount
{
    private Account $account;

    /**
     * ConvertLead constructor.
     * @param Account $account
     */
    public function __construct(Account $account)
    {
        $this->account = $account;
    }

    public function execute()
    {
        /*if ($this->lead->task_status === Lead::STATUS_COMPLETED) {
            return false;
        }*/

        try {
            DB::beginTransaction();

            $user = CloneAccountToUserFactory::create($this->account);

            if (!$user->save()) {
                DB::rollback();
                return null;
            }

            $customer = CloneAccountToCustomerFactory::create($this->account, $user);

            if (!$customer->save()) {
                DB::rollback();
                return null;
            }

            $address = CloneAccountToAddressFactory::create($this->account, $customer);

            if (!$address->save()) {
                DB::rollback();
                return null;
            }

            $client_contact =
                CloneAccountToContactFactory::create($this->account, $customer, $user);


            if (!$client_contact->save()) {
                DB::rollback();
                return null;
            }

            $this->account->domains->user_id = $user->id;
            $this->account->domains->customer_id = $customer->id;

            if (!$this->account->domains->save()) {
                DB::rollback();
                return null;
            }

            DB::commit();

            return $this->account;
        } catch (Exception $e) {
            Log::emergency($e->getMessage());
            echo $e->getMessage();
            DB::rollback();
            return null;
        }
    }
}
