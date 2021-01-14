<?php

namespace App\Models;

use App\Traits\Archiveable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subscription extends Model
{
    use SoftDeletes;
    use Archiveable;

    const ORDERWASCREATED = 1;
    const ORDERWASDELETED = 2;
    const CREDITWASCREATED = 3;
    const CREDITWASDELETED = 4;
    const CUSTOMERWASCREATED = 5;
    const CUSTOMERWASDELETED = 6;
    const INVOICEWASCREATED = 7;
    const INVOICEWASDELETED = 8;
    const PAYMENTWASCREATED = 9;
    const PAYMENTWASDELETED = 10;
    const QUOTEWASCREATED = 11;
    const QUOTEWASDELETED = 12;
    const LEADWASCREATED = 13;
    const ORDERWASBACKORDERED = 14;
    const ORDERWASHELD = 15;
    const DEALWASCREATED = 16;
    const DEALWASDELETED = 17;
    const PROJECTWASCREATED = 18;
    const PROJECTWASDELETED = 19;
    const TASKWASCREATED = 20;
    const TASKWASDELETED = 21;
    const PURCHASEORDERWASCREATED = 22;
    const PURCHASEORDERWASDELETED = 23;
    const CASEWASCREATED = 24;
    const CASEWASDELETED = 25;
    const EXPENSEWASDELETED = 26;
    const EXPENSEWASCREATED = 27;
    const LATEINVOICES = 28;
    const ORDERWASUPDATED = 29;
    const CREDITWASUPDATED = 30;
    const CUSTOMERWASUPDATED = 31;
    const INVOICEWASUPDATED = 32;
    const PAYMENTWASUPDATED = 33;
    const QUOTEWASUPDATED = 34;
    const LEADWASUPDATED = 35;
    const DEALWASUPDATED = 36;
    const PROJECTWASUPDATED = 37;
    const TASKWASUPDATED = 38;
    const PURCHASEORDERWASUPDATED = 39;
    const CASEWASUPDATED = 40;
    const EXPENSEWASUPDATED = 41;
    const COMPANYWASCREATED = 42;
    const COMPANYWASUPDATED = 43;
    const COMPANYWASDELETED = 44;


    protected $fillable = [
        'name',
        'target_url',
        'event_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }
}
