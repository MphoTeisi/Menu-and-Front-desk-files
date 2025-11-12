<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEnquiryRequest extends StoreEnquiryRequest
{
    /**
     * You can override rules if needed for update
     */
    public function rules(): array
    {
        $rules = parent::rules();

        // For update, we might want to make email unique except for current enquiry
        // Uncomment if you want unique emails per enquiry
        // if ($this->route('enquiry')) {
        //     $rules['email'] = 'nullable|email|max:255|unique:enquiries,email,' . $this->route('enquiry')->id;
        // }

        // For update, next_follow_up_date should be after or equal to enquiry_date, not necessarily today
        $rules['next_follow_up_date'] = 'nullable|date|after_or_equal:enquiry_date';

        // Remove last_follow_up_date from rules since it's auto-managed by the system
        unset($rules['last_follow_up_date']);

        return $rules;
    }

    /**
     * Customize messages for update if needed
     */
    public function messages(): array
    {
        $messages = parent::messages();

        // Update the message for next_follow_up_date
        $messages['next_follow_up_date.after_or_equal'] = 'Next follow-up date must be on or after the enquiry date.';

        return $messages;
    }
}
