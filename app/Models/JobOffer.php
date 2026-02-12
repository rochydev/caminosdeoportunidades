<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobOffer extends Model
{
    protected $table = 'job_offer';

    protected $fillable = [
        'company_account_id', 'category_id', 'contract_type_id', 'workday_type_id', 'modality_id', 'title', 'description', 'requirements', 'adaptations', 'city', 'is_adapted', 'status'
    ];

    public function company()
    {
        return $this->belongsTo(Account::class, 'company_account_id', 'id');
    }

    public function category()
    {
        return $this->belongsTo(JobCategory::class, 'category_id', 'id');
    }

    public function contractType()
    {
        return $this->belongsTo(ContractType::class, 'contract_type_id', 'id');
    }

    public function workdayType()
    {
        return $this->belongsTo(WorkdayType::class, 'workday_type_id', 'id');
    }

    public function modality()
    {
        return $this->belongsTo(ModalityType::class, 'modality_id', 'id');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'offer_tag', 'offer_id', 'tag_id');
    }

    public function disabilities()
    {
        return $this->belongsToMany(DisabilityType::class, 'offer_disability', 'offer_id', 'disability_type_id');
    }

    public function applications()
    {
        return $this->hasMany(Application::class, 'offer_id', 'id');
    }
}
