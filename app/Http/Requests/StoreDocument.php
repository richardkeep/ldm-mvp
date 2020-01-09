<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDocument extends FormRequest
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
        $rules = [
            'name'    =>  'bail|required|max:150',
            'url'    =>  'bail|size:2048|unique:documents',
            'type_document_id'    =>  'bail|required',
            'submitter_id'    =>  'bail|required',
        ];
        return $rules;
    }
}