<?php

namespace App\Requests\Product;

use App\Models\Product;
use App\Repositories\Base\BaseFormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends BaseFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $product = Product::find($this->product_id);
        return auth()->user()->can('update', $product);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'sku'         => 'required',
            'name'        => ['required', Rule::unique('products')->ignore($this->segment(3))],
            'price'       => 'required|numeric',
            'quantity'    => 'numeric',
            'cost'        => 'numeric',
            'description' => 'required'
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
