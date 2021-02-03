<?php

namespace Tests\Unit;

use App\Components\Reports\IncomeReport;
use App\Models\Account;
use Illuminate\Http\Request;
use Tests\TestCase;

class ReportTest extends TestCase
{

    public function test_income_report()
    {
        (new IncomeReport())->build(new Request(), Account::find(1));
    }
}