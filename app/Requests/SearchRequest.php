<?php

namespace App\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SearchRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'page'        => 'nullable',
            'column'      => 'nullable',
            'order'       => 'nullable',
            'per_page'    => 'nullable',
            'search_term' => 'nullable',
        ];
    }
}
