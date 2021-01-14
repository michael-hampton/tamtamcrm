<?php

namespace App\Designs;

class Dark
{

    public function header()
    {
        return '<div class="pt-4 px-4">
<div class="border-4 border-dark">
    <div class="inline-block mt-2" style="margin-bottom: 15px; width: 60%; margin-top: 20px;">
        $account_logo
    </div>
    <div class="inline-block text-right" style="width: 40%; margin-top: 20px">
        <div class="inline-block mr-4">
            $entity_labels
        </div>
        <div class="inline-block text-right">
            $entity_details
        </div>
    </div>
</div>
<div class="border-bottom border-dark"></div>';
    }

    public function body()
    {
        return '<div class="pt-4">
<div class="inline-block border-right border-dashed border-dark pt-4" style="width: 40%; margin-left: 40px;">
    $customer_details
</div>

<div class="inline-block pl-4" style="width: 20%">
 $account_details
$account_address
</div>
   
    
</div>
</div>

</div>

<div style="margin-top: 5px; margin-left: 30px">
            <h2>$pdf_type</h2>
        </div>

$table_here

<div class="mt-2 px-4 pb-4">
    <div class="inline-block" style="width: 70%">
        <div>
            <p>$entity.public_notes</p>
        </div>
    </div>
    $costs
</div>
<div class="mt-1 pb-4 px-4">
    <div class="inline-block" style="width: 70%">
        <div>
            <p class="font-weight-bold">$terms_label</p>
            <p>$terms</p>
        </div>
    </div>
    <div class="inline-block" style="width: 20%">
        <section class="py-2 pt-4 text-success border-top border-bottom border-dashed border-dark px-2 mt-1">
            <p class="text-right">$balance_due_label</p>
            <p class="text-right">$balance_due</p>
        </section>
    </div>
</div>
        <div class="border-bottom-4 ml-4 border-dark mt-4">
        <h4 class="font-weight-bold mb-4">Thanks for shopping with us</h4>
    </div>
    <div class="border-bottom border-dark mt-1"></div>
</div>

';
    }

    public function totals()
    {
        return '<div class="inline-block" style="width: 20%">
        <div class="px-3 mt-2">
            <div class="col-6 text-left">
                <span style="margin-right: 20px"> $discount_label </span> $discount<br>
                <span style="margin-right: 20px">$tax_label </span> $tax<br>
                <span style="margin-right: 20px"> $shipping_cost_label </span> $shipping_cost<br>
                <span style="margin-right: 20px"> $voucher_label </span> $voucher<br>
            </div>
        </div>
    </div>';
    }

    public function table()
    {
        return '<table class="w-100 table-auto mb-4 mt-4">
    <thead class="text-left border-dashed border-bottom border-dark">
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
