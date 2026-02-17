<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkdayType extends Model
{
    protected $table = 'workday_types';

    protected $fillable = ['name'];

    public function jobOffers()
    {
        return $this->hasMany(JobOffer::class, 'workday_type_id', 'id');
    }
}
