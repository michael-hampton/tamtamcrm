<?php

namespace App\Requests\ExpenseCategory;

use App\Models\ExpenseCategory;
use App\Repositories\Base\BaseFormRequest;
use Illuminate\Validation\Rule;

class UpdateCategoryRequest extends BaseFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $category = ExpenseCategory::find(request()->segment(3));
        return auth()->user()->can('update', $category);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['required', Rule::unique('expense_categories')->ignore(request()->segment(3))]
        ];
    }

}
