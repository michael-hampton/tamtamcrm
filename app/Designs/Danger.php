<?php

namespace App\Designs;

/**
 * @wip: Table margins act weird.
 */
class Danger
{

    public function header()
    {
        return '<div class="py-4 px-4 mt-4">
            <div style="width: 100%">
                         
                <div class="inline-block" style="width: 80%">
                    <div class="inline-block" style="width: 30%">
                        $customer_details
                    </div>
                    <div class="inline-block" style="width: 30%; margin-right: 60px">
                        $account_details
                    </div>
                    
                    <div class="inline-block" style="width: 30%; margin-right: 60px">
                         $account_address
                    </div>
                </div>
                
                <div class="inline-block" style="width: 10%">
    $account_logo
    </div>
</div>';
    }

    public function body()
    {
        return '<div class="mt-4">
<div class="inline-block" style="width: 60%">
    <h1 class="text-uppercase font-weight-bold">$pdf_type</h1>
</div>
<div class="inline-block text-left" style="width: 30%">
    <div class="inline-block">
        $entity_labels
    </div>
    <div class="inline-block text-right">
        $entity_details
    </div>
</div>
</div>

$table_here

<div class="border-top-4 border-danger">
<div class="px-4 pb-4" style="margin-top: 70px">
    <div class="inline-block" style="width: 65%">
        <div class="">
            <p>$entity.public_notes</p>
        </div>
    </div>
    $costs
</div>
<div class="mt-1 pb-4 px-4">
    <div style="width: 70%">
        <div>
            <p class="font-weight-bold">$terms_label</p>
            <p>$terms</p>
        </div>
    </div>
</div>
</div>
</div>

';
    }

    public function totals()
    {
        return '<div class="inline-block" style="width: 30%">
        <div class="px-3 mt-2">
            <div class="col-6 text-left">
                <span style="margin-right: 80px">$subtotal_label</span> $subtotal <br>
                <span style="margin-right: 80px">$discount_label</span> $discount <br>
                <span style="margin-right: 80px">$tax_label</span> $tax <br>
                <span style="margin-right: 80px">$shipping_cost_label</span> $shipping_cost <br>
                <span style="margin-right: 80px">$voucher_label</span> $voucher <br>
                <span style="margin-right: 80px">$balance_due_label</span> <span class="text-danger font-weight-bold">$balance_due</span> <br>
            </div>
        </div>
    </div>';
    }

    public function table()
    {
        return '<table class="w-100 table-auto mt-4 border-top-4 border-danger bg-white">
<thead class="text-left rounded">
    $product_table_header
</thead>
<tbody>
    $product_table_body
</tbody>
</table>';
    }

    public function task_table()
    {
        return '<table class="w-100 table-auto mt-4 border-top-4 border-danger bg-white">
    <thead class="text-left rounded">
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
</footer>
</body>
</html>';
    }

}
