<?php

namespace App\Http\Controllers;

use App\Repositories\Interfaces\PaymentMethodRepositoryInterface;
use App\Transformations\DepartmentTransformable;

class PaymentTypeController extends Controller
{

    use DepartmentTransformable;

    /**
     * @var DepartmentRepositoryInterface
     */
    private $paymentMethodRepo;

    /**
     * DepartmentController constructor.
     *
     * @param PaymentMethodRepositoryInterface $paymentMethodRepo
     */
    public function __construct(PaymentMethodRepositoryInterface $paymentMethodRepo)
    {
        $this->paymentMethodRepo = $paymentMethodRepo;
    }

    /**
     * @return mixed
     */
    public function index()
    {
        $list = $this->paymentMethodRepo->listPaymentMethods();
        return response()->json($list);
    }

}
