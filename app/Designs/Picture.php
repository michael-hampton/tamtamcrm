<?php

namespace App\Designs;

class Picture
{

    public function header()
    {
        return '<div class="px-4 py-4">
<div class="mt-4">
    <div class="inline-block" ref="logo" style="width: 50%">
        $account_logo
    </div>
    <div class="inline-block" style="width: 40%">
        <div class="inline-block-col mr-4">
            $entity_labels
        </div>
        <div class="inline-block text-right">
            $entity_details
        </div>
    </div>
</div>
</div>';
    }

    public function body()
    {
        return '<div class="bg-secondary h-auto p-4 pt-4">
<div>
    <div class="inline-block mr-4" style="width: 40%">
        <p class="text-uppercase text-warning">$to_label:</p>
        <div class="ml-4 mt-4 text-white">
            $customer_details
        </div>
    </div>
    <div class="inline-block" style="width: 30%">
        <p class="text-uppercase text-warning">$from_label:</p>
        <div class="ml-4 text-white">
            $account_details
        </div>
    </div>
</div>
</div>

<div style="margin-top: 5px; margin-left: 30px">
            <h2>$pdf_type</h2>
        </div>

$table_here

<div class="px-4 pb-4" style="margin-top: 80px">
<div class="inline-block mt-4" style="width: 70%">
    <div>
        <p>$entity.public_notes</p>
    </div>
</div>
$costs
</div>
</div>';
    }

    public function totals()
    {
        return '
<div class="inline-block" style="width: 30%">
    <div class="px-3 mt-2">
        <div class="col-6 text-right">
            <span style="margin-right: 20px">$discount_label</span> $discount<br>
            <span style="margin-right: 20px">$tax_label</span> $tax<br>
            <span style="margin-right: 20px">$shipping_cost_label</span> $shipping_cost<br>
            <span style="margin-right: 20px">$voucher_label</span> $voucher<br>
        </div>
    </div>
</div>
</div>
<div class="mt-4 pb-4 px-4">
<div class="inline-block" style="width: 70%">
    <div>
        <p class="font-weight-bold">$terms_label</p>
        <p>$terms</p>
    </div>
</div>
<div class="inline-block" style="width: 20%">
    <section class="bg-warning py-2 pt-4 pr-4 text-white px-2 mt-1">
        <p class="text-right">$balance_due_label</p>
        <p class="text-right">$balance_due</p>
    </section>
</div>
</div>';
    }

    public function table()
    {
        return '<table class="w-100 table-auto">
<thead class="text-left border-bottom-4 border-dark">
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
