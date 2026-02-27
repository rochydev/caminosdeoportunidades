<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CandidateCv extends Model
{
    protected $table = 'candidate_cv';

    protected $primaryKey = 'user_id';

    public $incrementing = false;

    protected $fillable = [
        'user_id',
        'title',
        'summary',
        'skills',
        'languages',
        'availability'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function experiences()
    {
        return $this->hasMany(CvExperience::class, 'cv_user_id', 'user_id');
    }

    public function educations()
    {
        return $this->hasMany(CvEducation::class, 'cv_user_id', 'user_id');
    }
}
