<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEnquiryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20|regex:/^[0-9+\-\s()]+$/',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500',
            'description' => 'nullable|string',

            'enquiry_date' => 'required|date|before_or_equal:today',
            'next_follow_up_date' => 'nullable|date|after_or_equal:enquiry_date',
            // Remove last_follow_up_date as it's auto-managed by the system
            'status' => 'required|in:active,closed,converted,lost',
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'The customer name is required.',
            'phone.required' => 'The phone number is required.',
            'phone.regex' => 'Please enter a valid phone number format.',
            'email.email' => 'Please enter a valid email address.',
            'enquiry_date.required' => 'The enquiry date is required.',
            'enquiry_date.date' => 'Please enter a valid date for enquiry date.',
            'enquiry_date.before_or_equal' => 'Enquiry date cannot be in the future.',
            'next_follow_up_date.after_or_equal' => 'Next follow-up date must be on or after the enquiry date.',
            'status.required' => 'Please select a status.',
            'status.in' => 'Please select a valid status.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'name' => 'customer name',
            'phone' => 'phone number',
            'enquiry_date' => 'enquiry date',
            'next_follow_up_date' => 'next follow-up date',
        ];
    }
}
