<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $table = 'accounts';

    protected $fillable = [
        'role', 'email', 'password_hash', 'status'
    ];

    public function candidateProfile()
    {
        return $this->hasOne(CandidateProfile::class, 'account_id', 'id');
    }

    public function candidateCv()
    {
        return $this->hasOne(CandidateCv::class, 'account_id', 'id');
    }

    public function companyProfile()
    {
        return $this->hasOne(CompanyProfile::class, 'account_id', 'id');
    }

    public function jobOffers()
    {
        return $this->hasMany(JobOffer::class, 'company_account_id', 'id');
    }

    public function applications()
    {
        return $this->hasMany(Application::class, 'candidate_account_id', 'id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'account_id', 'id');
    }

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class, 'actor_account_id', 'id');
    }
}
