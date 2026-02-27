<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $table = 'audit_log';

    protected $fillable = ['actor_user_id', 'entity_type', 'entity_id', 'action', 'details'];

    public function actor()
    {
        return $this->belongsTo(User::class, 'actor_user_id', 'id');
    }
}
