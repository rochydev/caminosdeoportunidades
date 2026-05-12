<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CvExperience extends Model
{
    protected $table = 'cv_experience';

    public $timestamps = false;

    protected $fillable = ['cv_user_id', 'company', 'position', 'start_date', 'end_date', 'description'];

    public function cv()
    {
        return $this->belongsTo(CandidateCv::class, 'cv_user_id', 'user_id');
    }
}
