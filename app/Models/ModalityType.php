<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ModalityType extends Model
{
    protected $table = 'modality_type';

    protected $fillable = ['name'];

    public function jobOffers()
    {
        return $this->hasMany(JobOffer::class, 'modality_id', 'id');
    }
}
