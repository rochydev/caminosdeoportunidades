<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CvEducation extends Model
{
    protected $table = 'cv_education';

    public $timestamps = false;

    protected $fillable = ['cv_user_id', 'institution', 'degree', 'start_date', 'end_date', 'description'];

    public function cv()
    {
        return $this->belongsTo(CandidateCv::class, 'cv_user_id', 'user_id');
    }
}
