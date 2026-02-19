<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CvExperience extends Model
{
    protected $table = 'cv_experience';

    protected $fillable = ['cv_account_id', 'company', 'position', 'start_date', 'end_date', 'description'];

    public function cv()
    {
        return $this->belongsTo(CandidateCv::class, 'cv_account_id', 'account_id');
    }
}
