<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class BankAccountRequest extends FormRequest
{

    protected $rules = [];
    protected $messages = [];

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ])
        );
    }

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
     * @return array<string, mixed>
     */
    public function rules()
    {
        $this->rules['bank']           = ['required', 'string'];
        $this->rules['account_name']   = ['required', 'string'];
        $this->rules['account_number'] = ['required', 'string'];
        $this->rules['is_default']     = ['nullable'];
        $this->rules['target']         = ['nullable'];


        return $this->rules;
    }
}
