<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use App\Http\Traits\ValidationResponse;

class StoreVariationTemplate extends FormRequest
{
    use ValidationResponse;
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return request()->isMethod('put') || request()->isMethod('patch') ? $this->updateRules() : $this->storeRules();
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function updateRules(): array
    {
        return [
            'name' => ['required']
        ];
    }

    protected function storeRules(): array
    {
        return [
            'name' => ['required','min:1','max:255','string'],
            'variation_values' => ['required','array','min:1',],
            'variation_values.*' => ['required']
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => trans('errors/variation_templates.name'),
            'name.min' => 'min must 4',
            'variation_values.*.required' => trans('errors/variation_templates.variation_values')
        ];
    }

//    public function attributes(): array
//    {
//        return [
//            'name' => __('errors/variation_templates.name')
//        ];
//    }

//    public function response(array $errors)
//    {
//        if ($this->ajax() || $this->wantsJson()) {
//            return response([
//                'errors' => $errors,
//                'status' => false,
//                'StatusCode' => 422,
//                'StatusType' => 'Unprocessable'
//            ],422);
//        } else {
//            return back()->withErrors($errors)->withInput();
//        }
//    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException($this->validationToJson($validator->errors()));
    }
}
