<?php

namespace App\Components\InvoiceCalculator;

use App\Models\Credit;

/**
 * Class LineItem
 * @package App\Components\InvoiceCalculator
 */
class LineItem extends BaseCalculator
{
    /**
     * @var float
     */
    private $sub_total = 0.00;

    /**
     * @var float
     */
    private float $total = 0.00;

    /**
     * @var int
     */
    private $line_item = 0;

    /**
     * @var int
     */
    private $quantity = 0;

    /**
     * @var int
     */
    private $attribute_id = 0;

    /**
     * @var int
     */
    private $type_id = 1;

    /**
     * @var float
     */
    private $unit_price = 0.00;

    /**
     * @var bool
     */
    private $is_amount_discount = true;

    /**
     * @var float
     */
    private $transaction_fee = 0.00;

    /**
     * @var bool
     */
    private $inclusive_taxes = false;

    private $tax_rate_name = '';

    private $tax_rate_id = 0;

    private $description = '';

    /**
     * @var float
     */
    private $unit_discount = 0.00;

    /**
     * @var float
     */
    private $unit_tax = 0.00;

    /**
     * @var float
     */
    private $discount_total = 0.00;

    /**
     * @var string
     */
    private $product_id = '';

    /**
     * @var string
     */
    private $notes = '';

    /**
     * @var float
     */
    private $tax_total = 0.00;

    private $types_to_ignore = [
        Credit::PAYMENT_TYPE
    ];

    /**
     * LineItem constructor.
     * @param $entity
     */
    public function __construct($entity = null)
    {
        parent::__construct($entity);
    }

    public function build()
    {
        if (in_array($this->type_id, $this->types_to_ignore)) {
            return $this;
        }

        $this->calculateSubTotal();
        $this->calculateDiscount();
        $this->calculateTax();
        return $this;
    }

    public function calculateSubTotal(): self
    {
        $this->sub_total = $this->applyQuantity($this->unit_price, $this->quantity);
        $this->total += $this->sub_total;
        return $this;
    }

    /**
     * @return LineItem
     */
    public function calculateDiscount(): self
    {
        $this->total -= $this->applyDiscount($this->sub_total, $this->unit_discount, $this->is_amount_discount);

        return $this;
    }

    /**
     * @return $this
     */
    public function calculateTax(): self
    {
        $this->tax_total += $this->applyTax($this->total, $this->unit_tax, $this->is_amount_discount);

        if ($this->inclusive_taxes) {
            $this->total += $this->tax_total;
        }

        return $this;
    }

    /**
     * @param bool $is_amount_discount
     * @return LineItem
     * @return LineItem
     */
    public function setIsAmountDiscount(bool $is_amount_discount = false): self
    {
        $this->is_amount_discount = $is_amount_discount;
        return $this;
    }

    /**
     * @param bool $inclusive_taxes
     * @return $this
     */
    public function setInclusiveTaxes(bool $inclusive_taxes): self
    {
        $this->inclusive_taxes = $inclusive_taxes;
        return $this;
    }

    public function toObject()
    {
        return (object)[
            'custom_value1'      => '',
            'custom_value2'      => '',
            'custom_value3'      => '',
            'custom_value4'      => '',
            'tax_rate_name'      => $this->getTaxRateName(),
            'tax_rate_id'        => $this->getTaxRateId(),
            'type_id'            => $this->getTypeId() ?: 1,
            'quantity'           => $this->getQuantity(),
            'notes'              => $this->getNotes(),
            'unit_price'         => $this->getUnitPrice(),
            'unit_discount'      => $this->getUnitDiscount(),
            'unit_tax'           => $this->getUnitTax(),
            'sub_total'          => $this->getTotal(),
            'line_total'         => $this->getSubTotal(),
            'discount_total'     => $this->getLineDiscountTotal(),
            'tax_total'          => $this->getLineTaxTotal(),
            'is_amount_discount' => $this->isAmountDiscount(),
            'product_id'         => $this->getProductId(),
            'attribute_id'       => $this->getAttributeId(),
            'transaction_fee'    => $this->getTransactionFee(),
            'description'        => $this->getDescription()
        ];
    }

    /**
     * @return string
     */
    public function getTaxRateName(): string
    {
        return $this->tax_rate_name;
    }

    /**
     * @param string $tax_rate_name
     * @return LineItem
     * @return LineItem
     */
    public function setTaxRateName(string $tax_rate_name): self
    {
        $this->tax_rate_name = $tax_rate_name;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getTaxRateId(): ?int
    {
        return $this->tax_rate_id;
    }

    /**
     * @param $tax_rate_id
     * @return LineItem
     */
    public function setTaxRateId($tax_rate_id): self
    {
        $this->tax_rate_id = $tax_rate_id;
        return $this;
    }

    /**
     * @return int
     */
    public function getTypeId(): int
    {
        return $this->type_id;
    }

    /**
     * @param int $type_id
     * @return LineItem
     * @return LineItem
     */
    public function setTypeId(int $type_id): self
    {
        $this->type_id = $type_id;
        return $this;
    }

    /**
     * @return int
     */
    public function getQuantity(): int
    {
        return $this->quantity;
    }

    /**
     * @param int $quantity
     * @return LineItem
     * @return LineItem
     */
    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;
        return $this;
    }

    /**
     * @return string
     */
    public function getNotes(): string
    {
        return $this->notes;
    }

    /**
     * @param string $notes
     * @return LineItem
     * @return LineItem
     */
    public function setNotes(string $notes): self
    {
        $this->notes = $notes;
        return $this;
    }

    /**
     * @return float
     */
    public function getUnitPrice(): float
    {
        return $this->unit_price;
    }

    /**
     * @param float $unit_price
     * @return $this
     */
    public function setUnitPrice(float $unit_price): self
    {
        $this->unit_price = $unit_price;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUnitDiscount()
    {
        return $this->unit_discount;
    }

    /**
     * @param float $unit_discount
     * @return LineItem
     * @return LineItem
     */
    public function setUnitDiscount(float $unit_discount): self
    {
        $this->unit_discount = $unit_discount;
        return $this;
    }

    /**
     * @return float
     */
    public function getUnitTax(): float
    {
        return $this->unit_tax;
    }

    /**
     * @param float $unit_tax
     * @return LineItem
     * @return LineItem
     */
    public function setUnitTax(float $unit_tax): self
    {
        $this->unit_tax = $unit_tax;
        return $this;
    }

    /**
     * @return int
     */
    public function getTotal(): int
    {
        return $this->total;
    }

    /**
     * @param float $total
     * @return LineItem
     */
    public function setTotal(float $total): self
    {
        $this->total = $total;
        return $this;
    }

    /**
     * @return float
     */
    public function getSubTotal(): float
    {
        return $this->sub_total;
    }

    /**
     * @param float $sub_total
     * @return $this
     */
    public function setSubTotal(float $sub_total): self
    {
        $this->sub_total = $sub_total;
        return $this;
    }

    /**
     * @return bool
     */
    public function isAmountDiscount(): bool
    {
        return $this->is_amount_discount;
    }

    /**
     * @return string
     */
    public function getProductId(): string
    {
        return $this->product_id;
    }

    /**
     * @param string $product_id
     * @return LineItem
     * @return LineItem
     */
    public function setProductId(string $product_id): self
    {
        $this->product_id = $product_id;
        return $this;
    }

    /**
     * @return int
     */
    public function getAttributeId(): int
    {
        return $this->attribute_id;
    }

    /**
     * @param int $attribute_id
     * @return LineItem
     * @return LineItem
     */
    public function setAttributeId(int $attribute_id): self
    {
        if (empty($attribute_id) || $attribute_id === 'null') {
            return $this;
        }

        $this->attribute_id = $attribute_id;
        return $this;
    }

    /**
     * @return float
     */
    public function getTransactionFee(): float
    {
        return $this->transaction_fee;
    }

    /**
     * @param float $transaction_fee
     * @return LineItem
     * @return LineItem
     */
    public function setTransactionFee(float $transaction_fee): self
    {
        $this->transaction_fee = $transaction_fee;
        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }
}
