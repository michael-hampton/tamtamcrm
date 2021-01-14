<?php

namespace App\Traits;

trait MonthlyPayments
{

    /**
     * @desc    Calculates the monthly payments of a loan
     *             based on the APR and Term.
     *
     * @param Float $fLoanAmount The loan amount.
     * @param Float $fAPR The annual interest rate.
     * @param Integer $iTerm The length of the loan in months.
     * @return    Float    Monthly Payment.
     */
    public function calcLoanPayments(float $fLoanAmount, float $fAPR, int $iTerm)
    {
        //***********************************************************
        //              INTEREST * ((1 + INTEREST) ^ TOTALPAYMENTS)
        // PMT = LOAN * -------------------------------------------
        //                  ((1 + INTEREST) ^ TOTALPAYMENTS) - 1
        //***********************************************************

        $value1 = $fAPR * pow((1 + $fAPR), $iTerm);
        $value2 = pow((1 + $fAPR), $iTerm) - 1;
        $pmt = $fLoanAmount * ($value1 / $value2);
        return $pmt;
    }

    function calcMortgagePayments($MORTGAGE, $AMORTYEARS, $AMORTMONTHS, $INRATE, $COMPOUND = 2, $FREQ, $DOWN)
    {
        $MORTGAGE = $MORTGAGE - $DOWN;
        $compound = $COMPOUND / 12;
        $monTime = ($AMORTYEARS * 12) + (1 * $AMORTMONTHS);
        $RATE = ($INRATE * 1.0) / 100;
        $yrRate = $RATE / $COMPOUND;
        $rdefine = pow((1.0 + $yrRate), $compound) - 1.0;
        $PAYMENT =
            ($MORTGAGE * $rdefine * (pow((1.0 + $rdefine), $monTime))) / ((pow((1.0 + $rdefine), $monTime)) - 1.0);
        if ($FREQ == 12) {
            return $PAYMENT;
        }
        if ($FREQ == 26) {
            return $PAYMENT / 2.0;
        }
        if ($FREQ == 52) {
            return $PAYMENT / 4.0;
        }
        if ($FREQ == 24) {
            $compound2 = $COMPOUND / $FREQ;
            $monTime2 = ($AMORTYEARS * $FREQ) + ($AMORTMONTHS * 2);
            $rdefine2 = pow((1.0 + $yrRate), $compound2) - 1.0;
            $PAYMENT2 = ($MORTGAGE * $rdefine2 * (pow((1.0 + $rdefine2), $monTime2))) /
                ((pow((1.0 + $rdefine2), $monTime2)) - 1.0);
            return $PAYMENT2;
        }
    }

    public function calculateMonthlyCost($number_of_months, $new_total)
    {
        $new_total = (float)$new_total;
        $monthly_rate = $new_total / $number_of_months;
        return number_format((float)$monthly_rate, 2, '.', '');
    }

    public function calculateTotal($value, $interest_rate)
    {
        $value = (float)$value;
        $interest_rate = (float)$interest_rate;
        $new_total = $value + (($interest_rate / 100) * $value);
        return number_format((float)$new_total, 2, '.', '');
    }

    public function calculateDownpayment($downpayment, $value)
    {
        $value = (float)$value;
        $downpayment_cost = ($downpayment / 100) * $value;
        return $downpayment_cost;
    }

}
