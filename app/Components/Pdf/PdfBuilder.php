<?php

namespace App\Components\Pdf;

use App\Models\Account;
use App\Models\Company;
use App\Models\Country;
use App\Models\Customer;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\Task;
use App\Models\User;
use App\Traits\DateFormatter;
use App\Traits\Money;
use Laracasts\Presenter\Exceptions\PresenterException;
use ReflectionClass;
use ReflectionException;

/**
 * Class PdfData
 * @package App
 */
class PdfBuilder
{
    use Money;
    use DateFormatter;

    protected $labels;
    protected $values;
    protected $data;
    protected $entity;
    protected $line_items;

    /**
     * PdfData constructor.
     * @param $entity
     * @throws ReflectionException
     */
    public function __construct($entity)
    {
        $this->entity = $entity;
        $this->class = strtolower((new ReflectionClass($this->entity))->getShortName());
    }

    public function buildContact($contact = null): self
    {
        if ($contact === null) {
            return $this;
        }

        $this->data['$contact.full_name'] = ['value' => $contact->present()->name(), 'label' => trans('texts.name')];
        $this->data['$contact.email'] = ['value' => $contact->email, 'label' => trans('texts.email_address')];
        $this->data['$contact.phone'] = ['value' => $contact->phone, 'label' => trans('texts.phone_number')];

        $this->data['$contact_name'] = ['value' => $contact->present()->name(), 'label' => trans('texts.contact_name')];
        $this->data['$contact.custom1'] = [
            'value' => isset($contact) ? $contact->custom_value1 : '&nbsp;',
            'label' => $this->makeCustomField('Contact', 'custom_value1')
        ];
        $this->data['$contact.custom2'] = [
            'value' => isset($contact) ? $contact->custom_value2 : '&nbsp;',
            'label' => $this->makeCustomField('Contact', 'custom_value2')
        ];
        $this->data['$contact.custom3'] = [
            'value' => isset($contact) ? $contact->custom_value3 : '&nbsp;',
            'label' => $this->makeCustomField('Contact', 'custom_value3')
        ];
        $this->data['$contact.custom4'] = [
            'value' => isset($contact) ? $contact->custom_value4 : '&nbsp;',
            'label' => $this->makeCustomField('Contact', 'custom_value4')
        ];
        return $this;
    }

    /**
     * @param $entity
     * @param $field
     * @return string
     */
    protected function makeCustomField($entity, $field): string
    {
        $custom_fields = $this->entity->account->custom_fields;

        if (!isset($custom_fields->{$entity})) {
            return '';
        }

        $new_array = array_filter(
            $custom_fields->{$entity},
            function ($obj) use ($field) {
                if ($field === $obj->name) {
                    return $obj;
                }
            }
        );

        $new_array = array_values($new_array);

        if (empty($new_array) || empty($new_array[0]->label)) {
            return '';
        }

        return $new_array[0]->label;
    }

    public function buildCustomer(Customer $customer): self
    {
        $this->data['$customer.number'] = [
            'value' => $customer->number ?: '&nbsp;',
            'label' => trans('texts.customer_id_number')
        ];
        $this->data['$customer.vat_number'] = [
            'value' => $customer->vat_number ?: '&nbsp;',
            'label' => trans('texts.vat_number')
        ];
        $this->data['$customer.website'] = [
            'value' => $customer->present()->website() ?: '&nbsp;',
            'label' => trans('texts.website')
        ];
        $this->data['$customer.phone'] = [
            'value' => $customer->present()->phone() ?: '&nbsp;',
            'label' => trans('texts.phone_number')
        ];
        $this->data['$customer.email'] = [
            'value' => isset(
                $customer->primary_contact()->first()->email
            ) ? $customer->primary_contact()->first()->email : '',
            'label' => trans('texts.email_address')
        ];

        $this->data['$customer.name'] = [
            'value' => $this->entity->present()->clientName() ?: '&nbsp;',
            'label' => trans('texts.customer_name')
        ];
        $this->data['$customer1'] = [
            'value' => $customer->custom_value1 ?: '&nbsp;',
            'label' => $this->makeCustomField('Customer', 'custom_value1')
        ];
        $this->data['$customer2'] = [
            'value' => $customer->custom_value2 ?: '&nbsp;',
            'label' => $this->makeCustomField('Customer', 'custom_value2')
        ];
        $this->data['$customer3'] = [
            'value' => $customer->custom_value3 ?: '&nbsp;',
            'label' => $this->makeCustomField('Customer', 'custom_value3')
        ];
        $this->data['$customer4'] = [
            'value' => $customer->custom_value4 ?: '&nbsp;',
            'label' => $this->makeCustomField('Customer', 'custom_value4')
        ];

        if (isset($customer->balance)) {
            $this->data['$customer.balance'] = [
                'value' => $customer->getFormattedCustomerBalance() ?: '&nbsp;',
                'label' => trans('texts.customer_balance')
            ];
        }

        if (isset($customer->paid_to_date)) {
            $this->data['$customer.paid_to_date'] = [
                'value' => $customer->getFormattedPaidToDate() ?: '&nbsp;',
                'label' => trans('texts.customer_paid_to_date')
            ];
        }

        return $this;
    }

    public function buildCompany(Company $company): self
    {
        $this->data['$customer.number'] = [
            'value' => $company->number ?: '&nbsp;',
            'label' => trans('texts.customer_id_number')
        ];
        $this->data['$customer.vat_number'] = [
            'value' => $company->vat_number ?: '&nbsp;',
            'label' => trans('texts.vat_number')
        ];
        $this->data['$customer.website'] = [
            'value' => $company->website ?: '&nbsp;',
            'label' => trans('texts.website')
        ];
        $this->data['$customer.phone'] = [
            'value' => $company->phone ?: '&nbsp;',
            'label' => trans('texts.phone_number')
        ];
        $this->data['$customer.email'] = [
            'value' => isset(
                $company->primary_contact()->first()->email
            ) ? $company->primary_contact()->first()->email : '',
            'label' => trans('texts.email_address')
        ];
        $this->data['$customer.name'] = [
            'value' => $company->name ?: '&nbsp;',
            'label' => trans('texts.customer_name')
        ];
        $this->data['$customer1'] = [
            'value' => $company->custom_value1 ?: '&nbsp;',
            'label' => $this->makeCustomField('Company', 'custom_value1')
        ];
        $this->data['$customer2'] = [
            'value' => $company->custom_value2 ?: '&nbsp;',
            'label' => $this->makeCustomField('Company', 'custom_value2')
        ];
        $this->data['$customer3'] = [
            'value' => $company->custom_value3 ?: '&nbsp;',
            'label' => $this->makeCustomField('Company', 'custom_value3')
        ];
        $this->data['$customer4'] = [
            'value' => $company->custom_value4 ?: '&nbsp;',
            'label' => $this->makeCustomField('Company', 'custom_value4')
        ];

        return $this;
    }

    public function buildStatement(): self
    {
        return $this;
    }

    public function buildTask(): self
    {
        //$this->data['$task.date'] = ['value' => '', 'label' => trans('texts.date')];
        $this->data['$task.name'] = ['value' => $this->entity->name, 'label' => trans('texts.name')];
        $this->data['$task.description'] = [
            'value' => $this->entity->description,
            'label' => trans('texts.description')
        ];
        $this->data['$task.hours'] = ['value' => 4, 'label' => trans('texts.hours')];
        $this->data['$task.rate'] = ['value' => 4, 'label' => trans('texts.rate')];
        $this->data['$task.cost'] = ['value' => 4, 'label' => trans('texts.total')];

        if ($this->class === 'deal') {
            $this->data['$task.value'] = ['value' => $this->entity->valued_at, 'label' => trans('texts.valued_at')];
        }

        return $this;
    }

    public function buildProduct(): self
    {
        $this->data['$product.date'] = ['value' => '', 'label' => trans('texts.date')];
        $this->data['$product.discount'] = ['value' => '', 'label' => trans('texts.discount')];
        $this->data['$product.product_key'] = ['value' => '', 'label' => trans('texts.product_name')];
        $this->data['$product.notes'] = ['value' => '', 'label' => trans('texts.notes')];
        $this->data['$product.cost'] = ['value' => '', 'label' => trans('texts.cost')];
        $this->data['$product.quantity'] = ['value' => '', 'label' => trans('texts.quantity')];
        $this->data['$product.tax'] = ['value' => '', 'label' => trans('texts.tax')];
        $this->data['$product.line_total'] = ['value' => '', 'label' => trans('texts.sub_total')];
        return $this;
    }

    public function buildCustomerAddress(Customer $customer): self
    {
        $this->data['$customer.address1'] = [
            'value' => $customer->present()->address() ?: '&nbsp;',
            'label' => trans('texts.address')
        ];

        $addresses = $customer->addresses;
        $billing = null;
        $shipping = null;

        if ($addresses->count() > 0) {
            foreach ($addresses as $address) {
                if ($address->address_type === 1) {
                    $billing = $address;
                } else {
                    $shipping = $address;
                }
            }
        }

        if (!empty($billing)) {
            $this->buildAddress($customer, $billing);
        }

        return $this;
    }

    public function buildAddress($entity, $address)
    {
        $this->data['$customer.address1'] = [
            'value' => $address->address_1 ?: '&nbsp;',
            'label' => trans('texts.address')
        ];
        $this->data['$customer.address2'] = [
            'value' => $address->address_2 ?: '&nbsp;',
            'label' => trans('texts.address')
        ];
        $this->data['$customer.city_state_postal'] = [
            'value' => isset($address->city) ? $entity->present()->cityStateZip(
                $address->city,
                $address->state_code,
                $address->zip,
                false
            ) : '&nbsp;',
            'label' => trans('texts.city_with_zip')
        ];
        $this->data['$postal_city_state'] = [
            'value' => $entity->present()->cityStateZip(
                $address->city,
                $address->state,
                $entity->postal_code,
                true
            ) ?: '&nbsp;',
            'label' => trans('texts.zip_with_city')
        ];
        $this->data['$customer.country'] = [
            'value' => isset($address->country->name) ? $address->country->name : 'No Country Set',
            'label' => trans('texts.country')
        ];

        return $this;
    }

    public function buildCompanyAddress(Company $company): self
    {
        $this->data['$customer.address1'] = [
            'value' => $company->address_1 ?: '&nbsp;',
            'label' => trans('texts.address')
        ];

        $this->buildAddress($company, $company);

        return $this;
    }

    public function buildAccount(Account $account): self
    {
        $this->data['$account.city_state_postal'] = [
            'value' => $account->present()->cityStateZip(
                $account->settings->city,
                $account->settings->state,
                $account->settings->postal_code,
                false
            ) ?: '&nbsp;',
            'label' => trans('texts.city_with_zip')
        ];
        $this->data['$account.postal_city_state'] = [
            'value' => $account->present()->cityStateZip(
                $account->settings->city,
                $account->settings->state,
                $account->settings->postal_code,
                true
            ) ?: '&nbsp;',
            'label' => trans('texts.zip_with_city')
        ];
        $this->data['$account.name'] = [
            'value' => $account->present()->name() ?: '&nbsp;',
            'label' => trans('texts.company_name')
        ];
        $this->data['$account.address1'] = [
            'value' => $account->settings->address1 ?: '&nbsp;',
            'label' => trans('texts.address1')
        ];
        $this->data['$account.address2'] = [
            'value' => $account->settings->address2 ?: '&nbsp;',
            'label' => trans('texts.address2')
        ];
        $this->data['$account.city'] = [
            'value' => $account->settings->city ?: '&nbsp;',
            'label' => trans('texts.city')
        ];
        $this->data['$account.state'] = [
            'value' => $account->settings->state ?: '&nbsp;',
            'label' => trans('texts.town')
        ];
        $this->data['$account.postal_code'] = [
            'value' => $account->settings->postal_code ?: '&nbsp;',
            'label' => trans('texts.zip')
        ];
        $this->data['$account.country'] = [
            'value' => Country::find($account->settings->country_id)->name ?: '&nbsp;',
            'label' => trans('texts.country')
        ];
        $this->data['$account.phone'] = [
            'value' => $account->settings->phone ?: '&nbsp;',
            'label' => trans('texts.phone_number')
        ];
        $this->data['$account.email'] = [
            'value' => $account->settings->email ?: '&nbsp;',
            'label' => trans('texts.email_address')
        ];
        $this->data['$account.vat_number'] = [
            'value' => $account->settings->vat_number ?: '&nbsp;',
            'label' => trans('texts.vat_number')
        ];
        $this->data['$account.number'] = [
            'value' => $account->settings->number ?: '&nbsp;',
            'label' => trans('texts.customer_id_number')
        ];
        $this->data['$account.website'] = [
            'value' => $account->settings->website ?: '&nbsp;',
            'label' => trans('texts.website')
        ];
        $this->data['$account.address'] = [
            'value' => $account->present()->address($account->settings) ?: '&nbsp;',
            'label' => trans('texts.address')
        ];

        $logo = $account->present()->logo($account->settings);

        $this->data['$account_logo'] = [
            'value' => "<img src='{$logo}' style='width: 100px; height: 100px;' alt='logo'>" ?: '&nbsp;',
            'label' => trans('texts.logo')
        ];
        $this->data['$account1'] = [
            'value' => $account->settings->custom_value1 ?: '&nbsp;',
            'label' => $this->makeCustomField('Account', 'custom_value1')
        ];
        $this->data['$account2'] = [
            'value' => $account->settings->custom_value2 ?: '&nbsp;',
            'label' => $this->makeCustomField('Account', 'custom_value2')
        ];
        $this->data['$account3'] = [
            'value' => $account->settings->custom_value3 ?: '&nbsp;',
            'label' => $this->makeCustomField('Account', 'custom_value3')
        ];
        $this->data['$account4'] = [
            'value' => $account->settings->custom_value4 ?: '&nbsp;',
            'label' => $this->makeCustomField('Account', 'custom_value4')
        ];
        return $this;
    }

    public function setTerms(?string $terms): self
    {
        $this->data['$terms'] = ['value' => $terms ?: '&nbsp;', 'label' => trans('texts.' . $this->class . '_terms')];
        return $this;
    }

    public function setFooter(?string $footer): self
    {
        $this->data['$footer'] = ['value' => $footer ?: '&nbsp;', 'label' => ''];
        return $this;
    }

    /**
     * @param User $user
     * @return $this
     * @throws PresenterException
     */
    public function setUser(User $user)
    {
        $this->data['$' . $this->class . '.agent'] = [
            'value' => $user->present()->name() ?: '&nbsp;',
            'label' => trans('texts.agent')
        ];
        return $this;
    }

    public function setTotal($customer, $total): self
    {
        $this->data['$entity_label'] = [
            'value' => '',
            'label' => $this->class
        ];

        $this->data['$' . $this->class . '.total'] = [
            'value' => $this->entity->getFormattedTotal() ?: '&nbsp;',
            'label' => trans('texts.' . $this->class . '_amount')
        ];
        return $this;
    }

    public function setBalance($customer, $balance): self
    {
        if (!isset($this->entity->balance)) {
            return $this;
        }

        $this->data['$' . $this->class . '.balance_due'] = [
            'value' => $this->entity->getFormattedBalance() ?: '&nbsp;',
            'label' => trans('texts.balance_due')
        ];
        $this->data['$balance_due'] = [
            'value' => $this->entity->getFormattedBalance() ?: '&nbsp;',
            'label' => trans('texts.balance_due')
        ];

        return $this;
    }

    public function setSubTotal($customer, $sub_total): self
    {
        if (!isset($this->entity->sub_total)) {
            return $this;
        }

        $this->data['$subtotal'] = [
            'value' => $this->entity->getFormattedSubtotal() ?: '&nbsp;',
            'label' => trans('texts.sub_total')
        ];

        return $this;
    }

    public function setDate($date): self
    {
        $this->data['$date'] = [
            'value' => $this->formatDate($this->entity, $date) ?: '&nbsp;',
            'label' => trans('texts.date')
        ];
        $this->data['$' . $this->class . '.date'] = [
            'value' => $this->formatDate($this->entity, $date) ?: '&nbsp;',
            'label' => trans('texts.date')
        ];
        return $this;
    }

    public function setDatetime($datetime): self
    {
        $this->data['$datetime'] = [
            'value' => $this->formatDatetime($this->entity, $datetime) ?: '&nbsp;',
            'label' => trans('texts.date_created')
        ];
        $this->data['$' . $this->class . '.datetime'] = [
            'value' => $this->formatDatetime($this->entity, $datetime) ?: '&nbsp;',
            'label' => trans('texts.date_created')
        ];
      
        return $this;
    }

    public function setInvoiceCustomValues(): self
    {
        $this->data['$' . $this->class . '.custom1'] = [
            'value' => $this->formatCustomField('Invoice', 'custom_value1', $this->entity->custom_value1) ?: '&nbsp;',
            'label' => $this->makeCustomField('Invoice', 'custom_value1')
        ];
        $this->data['$' . $this->class . '.custom2'] = [
            'value' => $this->formatCustomField('Invoice', 'custom_value2', $this->entity->custom_value2) ?: '&nbsp;',
            'label' => $this->makeCustomField('Invoice', 'custom_value2')
        ];
        $this->data['$' . $this->class . '.custom3'] = [
            'value' => $this->formatCustomField('Invoice', 'custom_value3', $this->entity->custom_value3) ?: '&nbsp;',
            'label' => $this->makeCustomField('Invoice', 'custom_value3')
        ];
        $this->data['$' . $this->class . '.custom4'] = [
            'value' => $this->formatCustomField('Invoice', 'custom_value4', $this->entity->custom_value4) ?: '&nbsp;',
            'label' => $this->makeCustomField('Invoice', 'custom_value4')
        ];

        return $this;
    }

    protected function formatCustomField($entity, $field, $value)
    {
        if (empty($value)) {
            return '';
        }

        $custom_fields = $this->entity->account->custom_fields;

        if (!isset($custom_fields->{$entity})) {
            return '';
        }

        $new_array = array_filter(
            $custom_fields->{$entity},
            function ($obj) use ($field) {
                if ($field === $obj->name) {
                    return $obj;
                }
            }
        );

        $new_array = array_values($new_array);

        switch ($new_array[0]->type) {
            case 'date';
                return $this->formatDate($this->entity, $value);
                break;
            case 'select':
            case 'text':
            case 'textarea':
                return (string)$value;
            case 'switch':
                return $value === true || $value === 1 || $value === '1' ? 'yes' : 'no';
                break;
        }

        return $value;
    }

    public function setPoNumber($po_number): self
    {
        $this->data['$' . $this->class . '.po_number'] = [
            'value' => $po_number ?: '&nbsp;',
            'label' => trans('texts.po_number')
        ];
        return $this;
    }

    public function setDiscount($customer, $discount): self
    {
        $this->data['$discount'] = [
            'value' => $this->formatCurrency($discount, $customer) ?: '&nbsp;',
            'label' => trans('texts.discount')
        ];
        return $this;
    }

    public function setShippingCost($customer, $shipping): self
    {
        $shipping = empty($shipping) ? 0 : $shipping;

        $this->data['$shipping_cost'] = [
            'value' => $this->formatCurrency($shipping, $customer) ?: '&nbsp;',
            'label' => trans('texts.shipping')
        ];
        return $this;
    }

    public function setVoucherCode($voucher_code): self
    {
        if (empty($voucher_code)) {
            return $this;
        }

        $this->data['$voucher'] = [
            'value' => $voucher_code ?: '&nbsp;',
            'label' => trans('texts.voucher')
        ];

        return $this;
    }

    public function setNumber($number): self
    {
        $this->data['$number'] = [
            'value' => $number ?: '&nbsp;',
            'label' => trans('texts.' . $this->class . '_number')
        ];
        $this->data['$' . $this->class . '.number'] = [
            'value' => $number ?: '&nbsp;',
            'label' => trans('texts.' . $this->class . '_number')
        ];
        $this->data['$' . $this->class . '.' . $this->class . '_no'] = $number;
        return $this;
    }

    public function setNotes($notes): self
    {
        $this->data['$entity.public_notes'] = [
            'value' => !empty($notes) ? nl2br($notes) : '&nbsp;',
            'label' => trans('texts.public_notes')
        ];
        return $this;
    }

    public function setTaxes($customer): self
    {
        $this->data['$tax'] = [
            'value' => $this->makeLineTaxes($customer),
            'label' => trans('texts.taxes')
        ];
        $this->data['$line_tax'] = [
            'value' => $this->makeLineTaxes($customer),
            'label' => trans('texts.taxes')
        ];

        return $this;
    }

    /**
     * @param Customer $customer
     * @return string
     */
    private function makeLineTaxes($customer): string
    {
        $data = '';

        $tax_map = $this->buildTaxMap();

        if (empty($tax_map)) {
            return $data;
        }

        foreach ($tax_map as $tax) {
            $data .= '<span>' . $this->formatCurrency($tax['total'], $customer) . '</span>';
        }

        return $data;
    }

    private function buildTaxMap()
    {
        if (!isset($this->entity->line_items)) {
            return [];
        }

        $line_items = $this->entity->line_items;

        $tax_collection = collect([]);

        foreach ($line_items as $key => $line_item) {
            if (empty($line_item->tax_total) || empty($line_item->tax_rate_name)) {
                continue;
            }

            $group_tax = [
                'key'      => $key,
                'total'    => $line_item->tax_total,
                'tax_name' => $line_item->tax_rate_name . ' ' . $line_item->unit_tax . '%'
            ];
            $tax_collection->push(collect($group_tax));
        }

        $keys = $tax_collection->pluck('key')->unique();

        foreach ($keys as $key) {
            $tax_name = $tax_collection->filter(
                function ($value, $k) use ($key) {
                    return $value['key'] == $key;
                }
            )->pluck('tax_name')->first();

            $total_line_tax = $tax_collection->filter(
                function ($value, $k) use ($key) {
                    return $value['key'] == $key;
                }
            )->sum('total');

            $tax_map[] = ['name' => $tax_name, 'total' => $total_line_tax];

            return $tax_map;
        }
    }

    /**
     * @param $due_date
     * @return $this
     */
    public function setDueDate($due_date): self
    {
        $this->data['$' . $this->class . '.due_date'] = [
            'value' => $this->formatDate($this->entity, $due_date) ?: '&nbsp;',
            'label' => trans('texts.due_date')
        ];
        $this->data['$due_date'] = &$this->data['$' . $this->class . '.due_date'];
        $this->data['$quote.valid_until'] = [
            'value' => $this->formatDate($this->entity, $due_date),
            'label' => trans('texts.valid_until')
        ];
        return $this;
    }

    public
    function getLabels()
    {
        return $this->labels;
    }

    public
    function getValues()
    {
        return $this->values;
    }

    /**
     * @param $labels
     * @param $html
     * @return string
     */
    public function parseLabels($labels, $html): string
    {
        return str_replace(array_keys($labels), array_values($labels), $html);
    }

    /**
     * @param $values
     * @param $html
     * @return string
     */
    public function parseValues($values, $html): string
    {
        return str_replace(array_keys($values), array_values($values), $html);
    }

    public function removeEmptyValues($values, $html): string
    {
        return str_replace($values, '', $html);
    }

    /**
     * @return mixed
     */
    public function getEntity()
    {
        return $this->entity;
    }

    protected function checkIfEmpty(array $line_items, $type = null): array
    {
        $empty_values = [];

        $rows = $type !== null ? $line_items[$type] : $line_items;

        foreach ($rows as $row) {
            foreach ($row as $column => $value) {
                if (empty($value)) {
                    $empty_values[$column] = !isset($empty[$column]) ? 1 : $empty[$column]++;
                }
            }
        }

        $return_array = [];

        foreach ($empty_values as $empty_column => $empty_count) {
            if ($empty_count === count($line_items[$type])) {
                $return_array[] = $empty_column;
            }
        }

        return $return_array;
    }

    protected function setDefaults(Customer $customer): self
    {
        $this->data['$entity_label'] = ['value' => '', 'label' => trans('texts.' . $this->class)];
        $this->data['$invoice.partial_due'] = [
            'value' => $this->formatCurrency(
                $this->entity->partial,
                $customer
            ) ?: '&nbsp;',
            'label' => trans('texts.partial_due_label')
        ];
        $this->data['$from'] = ['value' => '', 'label' => trans('texts.from')];
        $this->data['$to'] = ['value' => '', 'label' => trans('texts.to')];
        return $this;
    }

    /**
     * @param Customer $customer
     * @param $entity
     * @param string $table_type
     * @return $this
     */
    protected function transformLineItems($customer, $entity, $table_type = '$product'): self
    {
        if (!isset($entity->line_items) || empty($entity->line_items)) {
            return $this;
        }

        $this->line_items = [];

        foreach ($entity->line_items as $key => $item) {
            if (empty($item->type_id)) {
                $item->type_id = Invoice::PRODUCT_TYPE;
            }

            $this->line_items[$item->type_id][$key][$table_type . '.product_key'] = $item->product_id;

            if (is_numeric($item->product_id)) {
                switch ($item->type_id) {
                    case Invoice::PRODUCT_TYPE:
                        $product = Product::find($item->product_id);
                        $this->line_items[$item->type_id][$key][$table_type . '.product_key'] = $product->name;
                        break;

                    case Invoice::TASK_TYPE:
                        $product = Task::find($item->product_id);
                        $this->line_items[$item->type_id][$key][$table_type . '.product_key'] = $product->name;
                        break;

                    case Invoice::EXPENSE_TYPE:
                        $product = Expense::find($item->product_id);
                        $this->line_items[$item->type_id][$key][$table_type . '.product_key'] = !empty($product->category)
                            ? $product->category->name
                            : trans(
                                'texts.expense'
                            );

                        break;
                }

                if (!empty($item->attribute_id)) {
                    $product_attribute = ProductAttribute::find($item->attribute_id);

                    $product_attribute_values = array_column($product_attribute->attributesValues->toArray(), 'value');
                    $this->line_items[$item->type_id][$key][$table_type . '.product_key'] .= ' (' . implode(
                            ',',
                            $product_attribute_values
                        ) . ')';
                }
            }

            $this->line_items[$item->type_id][$key][$table_type . '.quantity'] = $item->quantity;
            $this->line_items[$item->type_id][$key][$table_type . '.notes'] = !empty($item->notes) ? $item->notes : '';

            if (empty($this->line_items[$item->type_id][$key][$table_type . '.notes']) && !empty($item->description)) {
                $this->line_items[$item->type_id][$key][$table_type . '.notes'] = $item->description;
            }

            $this->line_items[$item->type_id][$key][$table_type . '.cost'] = $this->formatCurrency(
                $item->unit_price,
                $customer
            );
            $this->line_items[$item->type_id][$key][$table_type . '.line_total'] = !empty($item->sub_total) ? $this->formatCurrency(
                $item->sub_total,
                $customer
            ) : 0;

            $this->line_items[$item->type_id][$key][$table_type . '.discount'] = '';

            if (isset($item->unit_discount) && $item->unit_discount > 0) {
                if (isset($item->is_amount_discount) && $item->is_amount_discount) {
                    $this->line_items[$item->type_id][$key][$table_type . '.discount'] = $this->formatCurrency(
                        $item->unit_discount,
                        $customer
                    );
                } else {
                    $this->line_items[$item->type_id][$key][$table_type . '.discount'] = $item->unit_discount . '%';
                }
            }

            if (isset($item->unit_tax) && $item->unit_tax > 0) {
                $this->line_items[$item->type_id][$key][$table_type . '.tax'] = round($item->unit_tax, 2) . "%";
                $this->line_items[$item->type_id][$key][$table_type . '.tax1'] = &$this->line_items[$item->type_id][$key][$table_type . '.tax_rate1'];
            }
        }

        return $this;
    }
}
