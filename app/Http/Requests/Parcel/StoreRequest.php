<?php

namespace App\Http\Requests\Parcel;

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
            'merchant_id'           => ['required','numeric'],
            // In simple hub-to-hub flow, these may be omitted
            'category_id'           => ['nullable','numeric'],
            'delivery_type_id'      => ['nullable','numeric'],
            'customer_name'         => ['required','string','max:191'],
            'customer_address'      => ['required','string','max:191'],
            'customer_phone'        => ['required','string','max:191'],
            'parcel_payment_method' => ['required','numeric']
        ];
    }

    public function messages() 
    {
        return [
            'parcel_payment_method'  => 'Please select your payment method'
        ];
    }
}
