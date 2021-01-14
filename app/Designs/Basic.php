<?php

namespace App\Designs;

class Basic
{
    public function header()
    {
        return '<style>body { padding-left: 20px; padding-right: 20px }</style> 

<div class="px-2 py-4">
<div>
    $account_logo
    <div class="inline-block" style="word-break: break-word">
        $account_details <br>
        $account_address
    </div>
</div>';
    }

    public function body()
    {
        return '<div class="inline-block mr-4 mt-4" style="width: 60%;">
                    $entity_details
        </div>

        <div class="inline-block">
            $customer_details
        </div>
        
        <div style="margin-top: 5px; margin-left: 30px">
            <h2>$pdf_type</h2>
        </div>

$table_here

<div style="margin-top: 65px">
<div class="inline-block col-6" style="width: 70%">
    <div class="">
        <p>$entity.public_notes</p>
        <div class="pt-4">
            <p class="font-weight-bold">$terms_label</p>
            <p>$terms</p>
        </div>
    </div>
</div>
 $costs
</div>
';
    }

    public function totals()
    {
        return '<div class="inline-block" style="width: 20%;">
    <div class="px-3">
        <div class="col-6 text-left">
            <span style="margin-right: 20px"> $discount_label </span>  $discount<br>
            <span style="margin-right: 20px">$tax_label</span> $tax<br>
            <span style="margin-right: 20px"> $balance_due_label </span>  $balance_due<br>
            <span style="margin-right: 20px"> $shipping_cost_label </span>  $shipping_cost<br>
            <span style="margin-right: 20px"> $voucher_label </span>  $voucher<br>
        </div>
    </div>
</div>';
    }

    public function table()
    {
        return '<table class="w-100 table-auto mt-4">
    <thead class="text-left bg-secondary">
        $product_table_header
    </thead>
    <tbody>
        $product_table_body
    </tbody>
</table>';
    }

    public function task_table()
    {
        return '
<table class="w-100 table-auto mt-4">
    <thead class="text-left">
        $task_table_header
    </thead>
    <tbody>
        $task_table_body
    </tbody>
</table>';
    }

    public function statement_table()
    {
        return '
<table class="w-100 table-auto mt-4">
    <thead class="text-left">
        $statement_table_header
    </thead>
    <tbody>
        $statement_table_body
    </tbody>
</table>';
    }

    public function footer()
    {
        return '
             <div style="width: 100%; margin-left: 20px">
             <div style="width: 45%" class="inline-block mb-2">
               $signature_here
           </div>
           
            <div style="width: 45%" class="inline-block mb-2">
               $client_signature_here
           </div>
</div>

        $pay_now_link
       
        
        <div class="footer_class py-4 px-4" style="page-break-inside: avoid;">
        $footer
        </div>
</body>
</html>';
    }

}
