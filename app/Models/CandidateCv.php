<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CandidateCv extends Model
{
    protected $table = 'candidate_cv';

    protected $primaryKey = 'account_id';

    public $incrementing = false;

    protected $fillable = [
        'account_id', 'title', 'summary', 'skills', 'languages', 'availability'
    ];

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id', 'id');
    }

    public function experiences()
    {
        return $this->hasMany(CvExperience::class, 'cv_account_id', 'account_id');
    }

    public function educations()
    {
        return $this->hasMany(CvEducation::class, 'cv_account_id', 'account_id');
    }
}
