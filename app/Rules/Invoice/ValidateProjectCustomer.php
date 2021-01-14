<?php


namespace App\Rules\Invoice;


use App\Models\Project;
use Illuminate\Contracts\Validation\Rule;

class ValidateProjectCustomer implements Rule
{
    private int $customer_id;

    public function __construct(int $customer_id)
    {
        $this->customer_id = $customer_id;
    }

    public function passes($attribute, $value)
    {
        if (empty($value)) {
            return true;
        }

        $project = Project::where('id', '=', $value)->first();

        if (empty($project)) {
            return false;
        }

        if ($project->customer_id !== $this->customer_id) {
            return false;
        }

        return true;
    }


    public function message()
    {
        return 'Invalid project used';
    }
}