<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobApplication extends Model
{
    protected $table = 'job_applications';

    protected $fillable = [
        'offer_id',
        'candidate_user_id',
        'status',
        'company_notes',
    ];

    public function offer()
    {
        return $this->belongsTo(JobOffer::class, 'offer_id', 'id');
    }

    public function candidate()
    {
        return $this->belongsTo(User::class, 'candidate_user_id', 'id');
    }
}
