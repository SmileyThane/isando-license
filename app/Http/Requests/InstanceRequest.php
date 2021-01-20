<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InstanceRequest extends FormRequest
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
            'status' => 'required|in:running,expired,suspended,terminated',
            'date_of_expiry' => 'required|date_format:Y-m-d',
            'max_customer' => 'required|min:1|numeric',
            'max_document' => 'required|min:1|numeric',
            'remarks' => 'required|min:10'
        ];
    }

    /**
     * Translate fields with user friendly name.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'status' => trans('instance.status'),
            'date_of_expiry' => trans('subscription.validity'),
            'max_customer' => trans('instance.max_customer'),
            'max_document' => trans('instance.max_document'),
            'remarks' => trans('instance.remarks')
        ];
    }
}
