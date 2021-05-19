<?php


namespace App\Jobs\Payment;


use App\Factory\CustomerContactFactory;
use App\Factory\CustomerFactory;
use App\Models\Account;
use App\Models\CompanyGateway;
use App\Models\Country;
use App\Models\CustomerGateway;
use App\Models\User;
use App\Repositories\CustomerContactRepository;
use App\Repositories\CustomerRepository;
use App\Traits\CreditPayment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Stripe\Collection;
use Stripe\Customer;
use Stripe\PaymentMethod;
use Stripe\StripeClient;

class StripeImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, CreditPayment;

    /**
     * @var array
     */
    private array $imports = [];

    private $stripe;

    /**
     * @var CompanyGateway
     */
    private CompanyGateway $company_gateway;

    /**
     * @var CustomerRepository
     */
    private CustomerRepository $customer_repository;

    /**
     * @var CustomerContactRepository
     */
    private CustomerContactRepository $customer_contact_repository;

    /**
     * @var User
     */
    private User $user;

    /**
     * @var Account
     */
    private Account $account;

    private const CREDIT_CARD = 1;
    private const ALIPAY = 10;
    private const SOFORT = 9;

    /**
     * StripeImport constructor.
     * @param CompanyGateway $company_gateway
     * @param CustomerRepository $customer_repository
     * @param CustomerContactRepository $customer_contact_repository
     * @param User $user
     * @param Account $account
     */
    public function __construct(CompanyGateway $company_gateway, CustomerRepository $customer_repository, CustomerContactRepository $customer_contact_repository, User $user, Account $account)
    {
        $this->company_gateway = $company_gateway;
        $this->customer_repository = $customer_repository;
        $this->customer_contact_repository = $customer_contact_repository;
        $this->user = $user;
        $this->account = $account;
    }

    private function setupConfig()
    {
        $config = $this->company_gateway->settings;

        $this->stripe = new StripeClient(
            $config->apiKey
        );

        return true;
    }

    public function handle()
    {
        $this->setupConfig();

        $customers = $this->stripe->customers->all(['limit' => 300]);

        $this->imports['gateways'] = [];

        foreach ($customers as $gateway_customer) {
            $customer = $this->mapCustomers($gateway_customer);
            $this->mapGateways($customer, $gateway_customer);
        }

        CustomerGateway::upsert($this->imports['gateways'], ['token', 'gateway_customer_reference', 'account_id'], ['meta']);
    }

    private function mapCustomers(Customer $stripe_customer)
    {
        $customer_data = [
            'name'  => !empty($stripe_customer->name) ? $stripe_customer->name : $stripe_customer->id,
            'phone' => !empty($stripe_customer->phone) ? $stripe_customer->phone : ''
        ];

        // create customer
        $customer = CustomerFactory::create($this->account, $this->user);
        $customer = $this->customer_repository->create($customer_data, $customer);

        $contact_data = [
            'first_name' => $customer->name,
            'email'      => !empty($stripe_customer->email) ? $stripe_customer->email : '',
            'phone'      => !empty($stripe_customer->phone) ? $stripe_customer->phone : ''
        ];

        // create contact
        $create_contact = CustomerContactFactory::create($customer->account, $customer->user, $customer);
        $this->customer_contact_repository->createContact($contact_data, $create_contact);

        if (!empty($customer->address)) {
            $address_data = [
                'address_1'    => !empty($customer->address->line1) ? $customer->address->line1 : '',
                'address_2'    => !empty($customer->address->line2) ? $customer->address->line2 : '',
                'city'         => !empty($customer->address->city) ? $customer->address->city : '',
                'state'        => !empty($customer->address->state) ? $customer->address->state : '',
                'address_type' => 1
            ];

            if (!empty($customer->address->country)) {

                $country = Country::where('iso', $customer->address->country)->first();

                if (!empty($country)) {
                    $address_data['country_id'] = $country->id;
                }

            }

            $customer->addresses()->create($address_data);
        }

        return $customer;
    }

    private function mapGateway($gateway, $gateway_customer, $customer, $type_id = null)
    {

        $data = [
            'account_id'                 => $this->account->id,
            'customer_id'                => $customer->id,
            'token'                      => $gateway->id,
            'gateway_customer_reference' => $gateway_customer->id,
            'company_gateway_id'         => $this->company_gateway->id,
            'gateway_type_id'            => $type_id,
            'meta'                       => []
        ];

        if ($type_id === self::CREDIT_CARD) {
            $data['meta'] = [
                'exp_month' => (string)$gateway->card->exp_month,
                'exp_year'  => (string)$gateway->card->exp_year,
                'brand'     => (string)$gateway->card->brand,
                'last4'     => (string)$gateway->card->last4,
                'type'      => 1
            ];

        } elseif ($type_id === 8) {
            $data['meta']['routing_number'] = $gateway->routing_number;
        }

        $data['meta'] = json_encode($data['meta']);

        return $data;
    }

    private function mapGateways($customer, $gateway_customer)
    {
        $cards = $this->stripe->paymentMethods->all([
            'customer' => $gateway_customer->id,
            'type'     => 'card',
        ]);

        foreach ($cards as $method) {
            $this->imports['gateways'][] = $this->mapGateway($method, $gateway_customer, $customer, self::CREDIT_CARD);
        }

        $alipay_methods = $this->stripe->paymentMethods->all([
            'customer' => $gateway_customer->id,
            'type'     => 'alipay',
        ]);

        foreach ($alipay_methods as $method) {
            $this->imports['gateways'][] = $this->mapGateway($method, $gateway_customer, $customer, self::ALIPAY);
        }

        $sofort_methods = $this->stripe->paymentMethods->all([
            'customer' => $gateway_customer->id,
            'type'     => 'sofort',
        ]);

        foreach ($sofort_methods as $method) {
            $this->imports['gateways'][] = $this->mapGateway($method, $gateway_customer, $customer, self::SOFORT);
        }

        $bank_accounts = $this->stripe->customers->allSources(
            $gateway_customer->id,
            ['object' => 'bank_account', 'limit' => 300]
        );

        foreach ($bank_accounts as $bank_account) {
            $this->imports['gateways'][] = $this->mapGateway($bank_account, $gateway_customer, $customer, 8);
        }
    }
}