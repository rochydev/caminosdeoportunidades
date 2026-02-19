<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobCategory extends Model
{
    protected $table = 'job_category';

    protected $fillable = ['name'];

    public function jobOffers()
    {
        return $this->hasMany(JobOffer::class, 'category_id', 'id');
    }
}
