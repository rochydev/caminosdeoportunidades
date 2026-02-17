<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContractType extends Model
{
    protected $table = 'contract_types';

    protected $fillable = ['name'];

    public function jobOffers()
    {
        return $this->hasMany(JobOffer::class, 'contract_type_id', 'id');
    }
}
