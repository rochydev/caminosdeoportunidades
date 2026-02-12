<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $table = 'notification';

    protected $fillable = ['account_id', 'type', 'title', 'body', 'is_read'];

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id', 'id');
    }
}
