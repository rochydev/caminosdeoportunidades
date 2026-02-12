<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CandidateProfile extends Model
{
    protected $table = 'candidate_profile';

    protected $primaryKey = 'account_id';

    public $incrementing = false;

    protected $fillable = [
        'account_id', 'first_name', 'last_name', 'phone', 'city', 'photo_url', 'disability_type_id', 'disability_degree', 'accessibility_needs'
    ];

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id', 'id');
    }

    public function disabilityType()
    {
        return $this->belongsTo(DisabilityType::class, 'disability_type_id', 'id');
    }
}
