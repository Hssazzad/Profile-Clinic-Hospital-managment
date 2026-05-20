<?php

namespace App\Http\Requests\Billing;

use Illuminate\Foundation\Http\FormRequest;

class CreateInvoiceRequest extends FormRequest
{
    public function authorize()
    {
        return true; // ??? auth logic ??? ???? ?????
    }

    public function rules()
    {
        return [
            'patient_id'      => 'required|integer|exists:patients,id',
            'patient_code'    => 'nullable|string',
            'patient_name'    => 'nullable|string',
            'patient_age'     => 'nullable|string',
            'mobile_no'       => 'nullable|string',
            'admission_id'    => 'nullable|integer|exists:nursing_admissions,id',

            'total_bill'      => 'required|numeric|min:1',
            'discount'        => 'nullable|numeric|min:0',
            'paid_amount'     => 'required|numeric|min:0',

            'payment_date'    => 'required|date',
            'payment_method'  => 'required|in:cash,mobile_banking,card,bank_transfer,cheque',
            'collected_by'    => 'nullable|string',

            'items'           => 'required|array|min:1',
            'items.*.category'      => 'nullable|integer',
            'items.*.category_name' => 'nullable|string',
            'items.*.service_name'  => 'required|string|max:255',
            'items.*.unit_price'    => 'required|numeric|min:0',
            'items.*.quantity'      => 'required|integer|min:1',
            'items.*.amount'        => 'required|numeric|min:0',
            'items.*.remarks'       => 'nullable|string',
        ];
    }

    public function messages()
    {
        return [
            'items.min' => '??????? ???? ????? ??? ???? ????',
            'paid_amount.min' => '??????? ?????????? ? ?? ???? ??? ????',
        ];
    }
}