<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    protected $table = 'application';

    protected $fillable = [
        'offer_id', 'candidate_account_id', 'status', 'company_notes'
    ];

    public function offer()
    {
        return $this->belongsTo(JobOffer::class, 'offer_id', 'id');
    }

    public function candidate()
    {
        return $this->belongsTo(Account::class, 'candidate_account_id', 'id');
    }
}
