<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFollowupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'notes' => 'required|string|min:5|max:1000',
            'followup_date' => 'required|date|before_or_equal:now', // Allow past dates up to current moment
            'next_follow_up_date' => 'nullable|date|after_or_equal:followup_date', // Must be after the actual follow-up date
            'update_status' => 'nullable|in:active,closed,converted,lost',
        ];
    }

    public function messages(): array
    {
        return [
            'notes.required' => 'Follow-up notes are required.',
            'notes.min' => 'Follow-up notes should be at least 5 characters.',
            'notes.max' => 'Follow-up notes should not exceed 1000 characters.',
            'followup_date.required' => 'Follow-up date is required.',
            'followup_date.before_or_equal' => 'Follow-up date cannot be in the future.',
            'next_follow_up_date.after_or_equal' => 'Next follow-up date must be on or after the follow-up date.',
            'update_status.in' => 'Please select a valid status.',
        ];
    }

    public function attributes(): array
    {
        return [
            'notes' => 'follow-up notes',
            'followup_date' => 'follow-up date',
            'next_follow_up_date' => 'next follow-up date',
            'update_status' => 'status update',
        ];
    }
}
