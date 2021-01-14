<?php

namespace App\Components\InvoiceCalculator;

class InvoiceCalculator
{
    /**
     * @var array
     */
    private $line_items = [];

    private $entity;

    /**
     * InvoiceCalculator constructor.
     * @param $entity
     */
    public function __construct($entity)
    {
        $this->entity = $entity;
    }

    /**
     * @return Invoice|null
     */
    public function build(): ?Invoice
    {
        if (empty($this->entity->line_items)) {
            return null;
        }

        $objInvoice = new Invoice($this->entity);

        foreach ($this->entity->line_items as $line_item) {
            $objLine = (new LineItem($this->entity))
                ->setQuantity($line_item->quantity)
                ->setAttributeId(isset($line_item->attribute_id) ? $line_item->attribute_id : 0)
                ->setUnitPrice($line_item->unit_price)
                ->setProductId($this->getEntityId($line_item))
                ->setSubTotal(isset($line_item->sub_total) ? $line_item->sub_total : 0)
                ->setTransactionFee(isset($line_item->transaction_fee) ? $line_item->transaction_fee : 0)
                ->setTypeId(!isset($line_item->type_id) ? 1 : $line_item->type_id)
                ->setUnitTax(isset($line_item->unit_tax) ? $line_item->unit_tax : 0)
                ->setTaxRateName(isset($line_item->tax_rate_name) ? $line_item->tax_rate_name : '')
                ->setTaxRateId(isset($line_item->tax_rate_id) ? $line_item->tax_rate_id : null)
                ->setUnitDiscount($line_item->unit_discount)
                ->setIsAmountDiscount(
                    isset($this->entity->is_amount_discount) ? $this->entity->is_amount_discount : false
                )
                ->setInclusiveTaxes($this->entity->account->settings->inclusive_taxes)
                ->setNotes(!empty($line_item->notes) ? $line_item->notes : '')
                ->setDescription(!empty($line_item->description) ? $line_item->description : '')
                ->build();


            $objInvoice->addItem($objLine->toObject());
        }

        $objInvoice
            ->setBalance($this->entity->balance)
            ->setInclusiveTaxes($this->entity->account->settings->inclusive_taxes)
            ->setTaxRate('tax_rate', $this->entity->tax_rate)
            ->setTaxRate('tax_2', !empty($this->entity->tax_2) ? $this->entity->tax_2 : 0)
            ->setTaxRate('tax_3', !empty($this->entity->tax_2) ? $this->entity->tax_2 : 0)
            ->setDiscountTotal(isset($this->entity->discount_total) ? $this->entity->discount_total : 0)
            ->setPartial($this->entity->partial)
            ->build();

        return $objInvoice;
    }

    private function getEntityId($line_item)
    {
        if (!empty($line_item->expense_id)) {
            return $line_item->expense_id;
        }

        if (!empty($line_item->task_id)) {
            return $line_item->task_id;
        }

        if (!empty($line_item->project_id)) {
            return $line_item->project_id;
        }

        return $line_item->product_id;
    }
}
