<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $table = 'notifications';

    protected $fillable = ['user_id', 'type', 'title', 'body', 'is_read'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
