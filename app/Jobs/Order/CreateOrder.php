<?php

namespace App\Jobs\Order;

use App\Factory\CustomerFactory;
use App\Factory\OrderFactory;
use App\Factory\TaskFactory;
use App\Models\Account;
use App\Models\Address;
use App\Models\Customer;
use App\Models\CustomerContact;
use App\Models\Order;
use App\Models\Task;
use App\Models\TaskStatus;
use App\Models\User;
use App\Repositories\CustomerContactRepository;
use App\Repositories\CustomerRepository;
use App\Repositories\OrderRepository;
use App\Repositories\TaskRepository;
use DateInterval;
use DateTime;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Capsule\Eloquent;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Request;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CreateOrder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var Order
     */
    private Order $order;

    /**
     * @var Task
     */
    private ?Task $task;

    /**
     * @var User
     */
    private User $user;

    /**
     * @var Account
     */
    private Account $account;


    private $request;

    /**
     * @var CustomerRepository
     */
    private CustomerRepository $customer_repo;

    /**
     * @var OrderRepository
     */
    private OrderRepository $order_repo;

    /**
     * @var TaskRepository
     */
    private TaskRepository $task_repo;

    /**
     * @var bool
     */
    private bool $is_deal;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    /**
     * CreateDeal constructor.
     * @param Account $account
     * @param User $user
     * @param Request $request
     * @param CustomerRepository $customer_repo
     * @param OrderRepository $order_repo
     * @param TaskRepository $task_repo
     * @param $is_deal
     */
    public function __construct(
        Account $account,
        User $user,
        $request,
        CustomerRepository $customer_repo,
        OrderRepository $order_repo,
        TaskRepository $task_repo,
        $is_deal
    ) {
        $this->request = $request;
        $this->user = $user;
        $this->account = $account;
        $this->customer_repo = $customer_repo;
        $this->order_repo = $order_repo;
        $this->task_repo = $task_repo;
        $this->is_deal = $is_deal;
    }

    public function handle()
    {
        DB::beginTransaction();

        try {
            $customer = !empty($this->request->customer_id) ? Customer::where(
                'id',
                '=',
                $this->request->customer_id
            )->first() : $this->saveCustomer();

            if (!$customer) {
                return null;
            }

            if (!$this->saveAddresses($customer)) {
                return null;
            }

            if ($customer->getSetting('create_task_on_order') === true) {
                $this->task = $this->saveTask($customer);

                if (!$this->task) {
                    return null;
                }
            }

            if (!empty($this->request->line_items)) {
                $order = $this->saveOrder($customer);

                if (!$order) {
                    Log::emergency('failed to create order');
                    die;

                    return null;
                }
            }

            DB::commit();
            return $order;
        } catch (Exception $e) {
            Log::emergency($e->getMessage());
            DB::rollback();
        }
    }

    private function saveCustomer(): ?Customer
    {
        $this->customer = CustomerFactory::create($this->account, $this->user);

        try {
            $contact = CustomerContact::where('email', '=', $this->request->email)->where(
                'account_id',
                '=',
                $this->account->id
            )->first();

            if (!empty($contact)) {
                $contact->update(
                    [
                        'first_name' => $this->request->first_name,
                        'last_name'  => $this->request->last_name,
                        'email'      => $this->request->email,
                        'phone'      => $this->request->phone,
                    ]
                );

                $this->customer = $contact->customer;
            }

            $this->customer = $this->customer_repo->save(
                [
                    'name'                   => $this->request->first_name . ' ' . $this->request->last_name,
                    'phone'                  => $this->request->phone,
                    'website'                => isset($this->request->website) ? $this->request->website : '',
                    'currency_id'            => 2,
                    'default_payment_method' => 1
                ],
                $this->customer
            );

            if (empty($contact)) {
                $contacts [] = [
                    'first_name' => $this->request->first_name,
                    'last_name'  => $this->request->last_name,
                    'email'      => $this->request->email,
                    'phone'      => $this->request->phone,
                ];

                (new CustomerContactRepository(new CustomerContact))->save($contacts, $this->customer);
            }

            return $this->customer;
        } catch (Exception $e) {
            Log::emergency($e->getMessage());
            DB::rollback();
            return null;
        }
    }

    /**
     * @param Customer $customer
     * @return bool
     */
    private function saveAddresses(Customer $customer): bool
    {
        try {
            if (!empty($this->request->billing)) {
                Address::updateOrCreate(
                    ['customer_id' => $customer->id, 'address_type' => 1],
                    [
                        'address_1'    => $this->request->billing['address_1'],
                        'address_2'    => !empty($this->request->billing['address_2']) ? $this->request->billing['address_2'] : '',
                        'zip'          => $this->request->billing['zip'],
                        'country_id'   => isset($this->request->billing['country_id']) ? $this->request->billing['country_id'] : 225,
                        'address_type' => 1
                    ]
                );
            }

            if (!empty($this->request->shipping)) {
                $address = Address::updateOrCreate(
                    ['customer_id' => $customer->id, 'address_type' => 2],
                    [
                        'address_1'    => $this->request->shipping['address_1'],
                        'address_2'    => !empty($this->request->shipping['address_2']) ? $this->request->shipping['address_2'] : '',
                        'zip'          => $this->request->shipping['zip'],
                        'country_id'   => isset($this->request->shipping['country_id']) ? $this->request->shipping['country_id'] : 225,
                        'address_type' => 2
                    ]
                );
            }

            return true;
        } catch (Exception $e) {
            Log::emergency($e->getMessage());
            DB::rollback();
            return false;
        }
    }

    /**
     * @param Customer $customer
     * @return Task|null
     */
    private function saveTask(Customer $customer): ?Task
    {
        try {
            $task = TaskFactory::create($this->user, $this->account);
            $date = new DateTime(); // Y-m-d
            $date->add(new DateInterval('P30D'));
            $due_date = $date->format('Y-m-d');

            $task = $this->task_repo->save(
                [
                    'due_date'       => $due_date,
                    'created_by'     => $this->user->id,
                    'source_type'    => $this->request->source_type,
                    'name'           => $this->request->title,
                    'description'    => isset($this->request->description) ? $this->request->description : '',
                    'customer_id'    => $customer->id,
                    'valued_at'      => $this->request->valued_at,
                    'task_status_id' => !empty($this->request->task_status)
                        ? $this->request->task_status
                        : TaskStatus::where(
                            'task_type',
                            '=',
                            1
                        )->first()->id
                ],
                $task
            );

            if (!empty($this->request->contributors)) {
                $task->users()->sync($this->request->input('contributors'));
            }

            return $task;
        } catch (Exception $e) {
            Log::emergency($e->getMessage());
            DB::rollback();
            return null;
        }
    }

    /**
     * @param Customer $customer
     * @return Order|null
     */
    private function saveOrder(Customer $customer): ?Order
    {
        try {
            $contacts = $customer->contacts->toArray();
            $invitations = [];

            foreach ($contacts as $contact) {
                $invitations[] = [
                    'contact_id' => $contact['id']
                ];
            }

            $this->order = OrderFactory::create($this->account, $this->user, $customer);

            $this->order = $this->order_repo->createOrder(
                [
                    'is_amount_discount' => !empty($this->request->is_amount_discount) ? $this->request->is_amount_discount : false,
                    'voucher_code'       => !empty($this->request->voucher_code) ? $this->request->voucher_code : null,
                    'gateway_fee'        => isset($this->request->gateway_fee) ? $this->request->gateway_fee : 0,
                    'transaction_fee'    => isset($this->request->transaction_fee) ? $this->request->transaction_fee : 0,
                    'shipping_cost'      => isset($this->request->shipping_cost) ? $this->request->shipping_cost : 0,
                    'invitations'        => $invitations,
                    'shipping_id'        => isset($this->request->shipping_id) ? $this->request->shipping_id : null,
                    'balance'            => $this->request->total,
                    'sub_total'          => $this->request->sub_total,
                    'total'              => $this->request->total,
                    //'tax_total'         => isset($this->request->tax_total) ? $this->request->tax_total : 0,
                    'discount_total'     => isset($this->request->discount_total) ? $this->request->discount_total : 0,
                    'tax_rate'           => isset($this->request->tax_rate) ? (float)str_replace(
                        '%',
                        '',
                        $this->request->tax_rate
                    ) : 0,
                    'line_items'         => $this->request->line_items,
                    'task_id'            => isset($this->task) ? $this->task->id : null,
                    'date'               => date('Y-m-d')
                ],
                $this->order
            );

            $subject = $this->order->customer->getSetting('email_subject_order_received');
            $body = $this->order->customer->getSetting('email_template_order_received');

            $this->order->service()->sendEmail(null, $subject, $body);

            return $this->order;
        } catch (Exception $e) {
            Log::emergency($e->getMessage());
            DB::rollback();
            return null;
        }
    }
}
