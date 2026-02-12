<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CvEducation extends Model
{
    protected $table = 'cv_education';

    protected $fillable = ['cv_account_id', 'institution', 'degree', 'start_date', 'end_date', 'description'];

    public function cv()
    {
        return $this->belongsTo(CandidateCv::class, 'cv_account_id', 'account_id');
    }
}
