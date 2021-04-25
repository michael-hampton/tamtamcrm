<?php


namespace App\ViewModels;


use App\Models\Lead;

class LeadViewModel extends ViewModel
{

    /**
     * @var Lead 
     */
    private Lead $lead;

    /**
     * LeadViewModel constructor.
     * @param Lead $lead
     */
    public function __construct(Lead $lead)
    {
        $this->lead = $lead;
    }

    /**
     * @return string
     */
    /**
     * @return string
     */
    public function name()
    {
        return $this->lead->first_name . ' ' . $this->lead->last_name;
    }

    public function address()
    {
        $fields = ['address_1', 'address_2', 'city', 'country'];
        $str = '';

        foreach ($fields as $field) {
            if (empty($this->lead->{$field})) {
                continue;
            }

            if ($field === 'country') {
                $country = $this->lead->country;
                $str .= $country->name . '<br/>';
                continue;
            }

            $str .= $this->lead->{$field} . '<br/>';
        }

        return $str;
    }

    public function email()
    {
        return $this->lead->email;
    }

    public function phone()
    {
        return $this->lead->phone ?: '';
    }

    public function website()
    {
        return $this->lead->website ?: '';
    }
}