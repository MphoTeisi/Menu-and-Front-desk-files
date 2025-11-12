<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EnquiryFollowup extends Model
{
    use HasFactory;

    protected $fillable = [
        'enquiry_id',
        'description',
        'followup_date',
    ];

    protected $casts = [
        'followup_date' => 'datetime',
    ];

    /**
     * Relationship: Followup belongs to an Enquiry
     */
    public function enquiry()
    {
        return $this->belongsTo(Enquiry::class);
    }

    /**
     * Scope: Get follow-ups scheduled for today or later
     */
    public function scopeUpcoming($query)
    {
        return $query->where('followup_date', '>=', now())->orderBy('followup_date');
    }
}
