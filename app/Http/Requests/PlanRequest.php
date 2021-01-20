<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PlanRequest extends FormRequest
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
        $id = $this->route('id');

        $rules = [
            'max_customer' => 'required|numeric|min:0',          
            'description' => 'required'
        ];
 /* 'max_document' => 'required|numeric|min:0',*/
        if ($this->method() === 'POST') {
            $rules['name'] = 'required|unique:plans';
            $rules['code'] = 'required|unique:plans';
        } elseif ($this->method() === 'PATCH') {
            $rules['name'] = 'required|unique:plans,name,'.$id.',id';
            $rules['code'] = 'required|unique:plans,code,'.$id.',id';
        }

        return $rules;
    }

    /**
     * Translate fields with user friendly name.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'name' => trans('plan.name'),
            'code' => trans('plan.code'),
            'max_customer' => trans('plan.max_customer'),
            'max_document' => trans('plan.max_document'),
            'description' => trans('plan.description')
        ];
    }
}
