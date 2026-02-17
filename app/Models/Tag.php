<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $table = 'tags';

    protected $fillable = ['name'];

    public function jobOffers()
    {
        return $this->belongsToMany(JobOffer::class, 'offer_tag', 'tag_id', 'offer_id');
    }
}
