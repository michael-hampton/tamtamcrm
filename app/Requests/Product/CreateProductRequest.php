<?php

namespace App\Requests\Product;

use App\Models\Product;
use App\Repositories\Base\BaseFormRequest;

class CreateProductRequest extends BaseFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user()->can('create', Product::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'company_id'  => 'required|numeric|min:1|not_in:0',
            'sku'         => 'required|string',
            'name'        => ['required', 'unique:products'],
            'description' => 'required:string',
            'price'       => 'required|numeric',
            'quantity'    => 'numeric',
            'cost'        => 'numeric',
        ];
    }

    protected function prepareForValidation()
    {
        $input = $this->all();
        if (!isset($input['quantity']) || $input['quantity'] < 1) {
            $input['quantity'] = 1;
        }
        $this->replace($input);
    }

}
