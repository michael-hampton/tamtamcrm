<?php


namespace App\Traits;


trait Balancer
{

    /**
     * @param $amount
     * @return Balancer
     * @return Balancer
     */
    public function reduceBalance($amount)
    {
        $amount = $this->balance < 0 ? $amount * -1 : $amount;
        $this->balance -= $amount;

        if ($this->balance === 0.0 && get_class($this) === 'App\Models\Invoice') {
            $this->setStatus(self::STATUS_PAID);
            $this->date_to_send = null;
        }

        $this->save();
        return $this;
    }

    public function reduceCreditBalance(float $amount)
    {
        $this->customer->credit_balance -= $amount;

        $this->save();
        return $this;
    }

    /**
     * @param float $amount
     * @return float
     */
    public function increaseBalance(float $amount): float
    {
        $amount = $this->balance < 0 ? $amount * -1 : $amount;

        $balance = $this->balance + $amount;

        return $this->setBalance($balance);
    }

    /**
     * @param float $balance
     * @return float
     * @return float
     */
    public function setBalance(float $balance)
    {
        $this->balance = (float)$balance;

        return $this->balance;
    }

    public function setTotal(float $total)
    {
        $this->total = (float)$total;
    }
}