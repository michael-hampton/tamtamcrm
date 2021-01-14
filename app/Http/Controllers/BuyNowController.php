<?php


namespace App\Http\Controllers;


use App\Factory\CustomerFactory;
use App\Jobs\Invoice\CreateInvoice;
use App\Models\Account;
use App\Models\Customer;
use App\Models\CustomerContact;
use App\Models\Product;
use App\Models\User;
use App\Repositories\CustomerRepository;
use App\Requests\Customer\CreateCustomerRequest;
use Exception;
use Illuminate\Http\Request;

class BuyNowController
{

    public function buyNowTrigger(Request $request)
    {
        $product = Product::where('id', '=', $request->input('product_id'))->first();
        $account = Account::where('id', '=', $request->input('account_id'))->first();

        if (!empty($request->input('contact'))) {
            $contact = CustomerContact::where('contact_key', '=', $request->input('contact'))->first();
            $customer = $contact->customer;
        } else {
            $customer = $this->createCustomer($request, $account, $product->user);
        }

        CreateInvoice::dispatchNow($account, $customer, $product, $request->all());

        return redirect('http://' . config('taskmanager.app_domain') . '/#/invoices');
    }

    /**
     * @param CreateCustomerRequest $request
     * @param Account $account
     * @param User $user
     * @return Customer
     * @throws Exception
     */
    private function createCustomer(CreateCustomerRequest $request, Account $account, User $user): Customer
    {
        $customer = CustomerFactory::create($account, $user);
        $customer = (new CustomerRepository(new Customer()))->save($request->all(), $customer);
        return $customer;
    }
}