<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyProfile extends Model
{
    protected $table = 'company_profile';

    protected $primaryKey = 'account_id';

    public $incrementing = false;

    protected $fillable = [
        'account_id', 'company_name', 'description', 'sector', 'city', 'contact_phone', 'website', 'logo_url', 'offers_adapted_positions', 'offers_remote_work', 'offers_reasonable_adjustments', 'validation_status'
    ];

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id', 'id');
    }
}
