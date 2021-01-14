<?php

namespace App\Designs;

class Warning
{

    public function header()
    {
        return '<div class="header_class bg-warning" style="page-break-inside: avoid;">
<div class="inline-block ml-3" style="width: 50%">
	<h1 class="text-white font-weight-bold">$account.name</h1>
</div>
<div class="inline-block mt-2 mb-3" style="width: 40%">
	<div class="inline-block text-white">
		$entity_labels
	</div>
	<div class="inline-block text-left text-white">
		$entity_details
	</div>
</div>
</div>';
    }

    public function body()
    {
        return '<div class="px-4 pt-4 header-space" style="width: 100%">
            <div class="inline-block p-3" style="width: 50%">
                $account_logo
            </div>
            <div class="inline-block" style="width: 40%">
                $customer_details
            </div>
        </div>

<div style="margin-top: 5px; float: left; margin-left: 30px">
<h2>$pdf_type</h2>
</div>

<div class="px-4 pt-4 pb-4">

$table_here

<div class="px-4 mt-4 w-100" style="page-break-inside: avoid;">
			        <div class="inline-block" style="width: 70%">
			            $entity.public_notes
			        </div>
			        $costs
			    </div>
			    <div class="px-4 mt-4 mt-4 inline-block" style="page-break-inside: avoid; width: 100%">
			        <div style="page-break-inside: avoid; width: 70%">
			            <p class="font-weight-bold">$terms_label</p>
			            $terms
			        </div>
			    </div>
			    <div class="px-4 py-2 bg-secondary text-white inline-block" style="page-break-inside: avoid; width: 20%; float: right">
			        <div class="inline-block" style="width: 70%"></div>
			        <div class="" style="page-break-inside: avoid; width: 100%" >
			            <div style="page-break-inside: avoid;">
			                <p class="font-weight-bold">$balance_due_label</p>
			            </div>
			            <p>$balance_due</p>
			        </div>
			    </div>
</div>
<div class="footer-space"></div>
';
    }

    public function totals()
    {
        return '<div class="inline-block" style="page-break-inside: avoid; width: 20%">
			            <div class="col-6 text-left" style="page-break-inside: avoid;">
			            	<span style="margin-right: 20px">$discount_label</span> <span style="margin-left: 16px"> $discount </span> <br>
			                <span style="margin-right: 20px">$tax_label</span> <span style="margin-left: 16px">$tax</span> <br>
			                <span style="margin-right: 20px">$shipping_cost_label</span> <span style="margin-left: 16px"> $shipping_cost</span> <br>
			                <span style="margin-right: 20px">$voucher_label</span> <span style="margin-left: 16px">$voucher</span> <br>
			            </div>
			        </div>';
    }

    public function table()
    {
        return '<table class="w-100 table-auto mt-4">
    <thead class="text-left text-white bg-secondary">
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
		 <div style="width: 100%; margin-left: 20px; margin-top: 90px;">
             <div style="width: 45%;" class="inline-block mb-2">
               $signature_here
           </div>
           
            <div style="width: 45%" class="inline-block mb-2">
               $client_signature_here
           </div>
</div>

$pay_now_link
		
		<div class="footer_class bg-warning py-4 px-4 pt-4" style="page-break-inside: avoid; width: 100%"> 
			    <div class="mt-2 text-center" style="width: 100%; margin-left: 20%">
			        <div class="inline-block text-white" style="width: 36%">
			            $account_details
			        </div>
			        <div class="inline-block text-left text-white" style="width: 30%">
			            $account_address
			        </div>
			    </div>
			    
			    <div class="mt-4">
			    $footer
</div>
			</div>
               
		
			</html>
		';
    }

}
