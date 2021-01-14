<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Message;
use App\Repositories\Interfaces\CustomerRepositoryInterface;
use App\Repositories\Interfaces\MessageRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Requests\CreateMessageRequest;
use App\Transformations\MessageTransformable;
use App\Transformations\MessageUserTransformable;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{

    use MessageUserTransformable, MessageTransformable;

    /**
     * @var MessageRepositoryInterface
     */
    private $messageRepo;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepo;

    /**
     * @var UserRepositoryInterface
     */
    private $userRepo;

    /**
     * MessageController constructor.
     * @param MessageRepositoryInterface $messageRepository
     * CustomerRepositoryInterface $customerRepository
     * UserRepositoryInterface $userRepository
     * @param CustomerRepositoryInterface $customerRepository
     * @param UserRepositoryInterface $userRepository
     */
    public function __construct(
        MessageRepositoryInterface $messageRepository,
        CustomerRepositoryInterface $customerRepository,
        UserRepositoryInterface $userRepository
    ) {
        $this->messageRepo = $messageRepository;
        $this->customerRepo = $customerRepository;
        $this->userRepo = $userRepository;
    }

    public function getCustomers()
    {
        $customerList = $this->customerRepo->getAll();
        $user = Auth::user();

        $customers = $customerList->map(
            function (Customer $customer) use ($user) {
                return $this->transformUser($customer, $user);
            }
        )->all();

        return response()->json($customers);
    }

    /**
     *
     * @param int $customer_id
     * @return type
     */
    public function index(int $customer_id)
    {
        $user = Auth::user();
        $customer = $this->customerRepo->findCustomerById($customer_id);
        $messageList = $this->messageRepo->getMessagesForCustomer($customer, $user);


        $messages = $messageList->map(
            function (Message $message) use ($user, $customer) {
                return $this->transformMessage($message, $user, $customer);
            }
        )->all();

        return response()->json($messages);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CreateMessageRequest $request
     * @return Response
     */
    public function store(CreateMessageRequest $request)
    {
        $message = $this->messageRepo->createMessage($request->except('_token', '_method', 'when'));
        return $message->toJson();
    }

}
