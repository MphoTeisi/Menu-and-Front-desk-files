<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enquiry extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'address',
        'description',
        'enquiry_date',
        'next_follow_up_date',
        'last_follow_up_date',
        'status',
    ];

       protected $casts = [
    'enquiry_date' => 'date',
    'next_follow_up_date' => 'date',
    'last_follow_up_date' => 'date',
    // ... other casts
];
/**
     * Relationship: Enquiry has many follow-ups
     */
    public function followups()
    {
        return $this->hasMany(EnquiryFollowup::class)->latest('followup_date');
    }

    /**
     * Scope: Get only active enquiries
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope: Get enquiries with upcoming follow-ups
     */
    public function scopeWithUpcomingFollowups($query)
    {
        return $query->where('next_follow_up_date', '>=', now());
    }

}
