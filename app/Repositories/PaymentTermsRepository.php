<?php

namespace App\Repositories;

use App\Models\PaymentTerms;
use App\Repositories\Base\BaseRepository;

class PaymentTermsRepository extends BaseRepository
{
    /**
     * GroupRepository constructor.
     * @param PaymentTerms $payment_terms
     */
    public function __construct(PaymentTerms $payment_terms)
    {
        parent::__construct($payment_terms);
    }

    /**
     * Gets the class name.
     *
     * @return     string  The class name.
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param int $id
     * @return PaymentTerms
     */
    public function findPaymentTermsById(int $id): PaymentTerms
    {
        return $this->findOneOrFail($id);
    }

    public function save($data, PaymentTerms $payment_terms): ?PaymentTerms
    {
        $payment_terms->fill($data);
        $payment_terms->save();

        return $payment_terms;
    }
}
