<?php

namespace App\Http\Requests\Business;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
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
            'name' => 'required|max:255',
            'currency_id' => 'required|numeric',
            'country' => 'required|max:255',
            'state' => 'required|max:255',
            'city' => 'required|max:255',
            'zip_code' => 'required|max:255',
            'landmark' => 'required|max:255',
            'time_zone' => 'required|max:255',
            'surname' => 'max:10',
            'email' => 'sometimes|nullable|email|unique:users|max:255',
            'first_name' => 'required|max:255',
            'username' => 'required|min:4|max:255|unique:users',
            'password' => 'required|min:4|max:255',
            'fy_start_month' => 'required',
            'accounting_method' => 'required',
        ];
    }

    public function message()
    {
        return [
            'name.required' => __('validation.required', ['attribute' => __('business.business_name')]),
            'name.currency_id' => __('validation.required', ['attribute' => __('business.currency')]),
            'country.required' => __('validation.required', ['attribute' => __('business.country')]),
            'state.required' => __('validation.required', ['attribute' => __('business.state')]),
            'city.required' => __('validation.required', ['attribute' => __('business.city')]),
            'zip_code.required' => __('validation.required', ['attribute' => __('business.zip_code')]),
            'landmark.required' => __('validation.required', ['attribute' => __('business.landmark')]),
            'time_zone.required' => __('validation.required', ['attribute' => __('business.time_zone')]),
            'email.email' => __('validation.email', ['attribute' => __('business.email')]),
            'email.email' => __('validation.unique', ['attribute' => __('business.email')]),
            'first_name.required' => __('validation.required', ['attribute' =>
            __('business.first_name')]),
            'username.required' => __('validation.required', ['attribute' => __('business.username')]),
            'username.min' => __('validation.min', ['attribute' => __('business.username')]),
            'password.required' => __('validation.required', ['attribute' => __('business.username')]),
            'password.min' => __('validation.min', ['attribute' => __('business.username')]),
            'fy_start_month.required' => __('validation.required', ['attribute' => __('business.fy_start_month')]),
            'accounting_method.required' => __('validation.required', ['attribute' => __('business.accounting_method')]),
        ];
    }
}
