<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DisabilityType extends Model
{
    protected $table = 'disability_type';

    protected $fillable = ['name'];

    public function candidateProfiles()
    {
        return $this->hasMany(CandidateProfile::class, 'disability_type_id', 'id');
    }

    public function jobOffers()
    {
        return $this->belongsToMany(JobOffer::class, 'offer_disability', 'disability_type_id', 'offer_id');
    }
}
