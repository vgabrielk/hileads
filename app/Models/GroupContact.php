<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GroupContact extends Model
{
    protected $fillable = [
        'group_id',
        'contact_jid',
        'contact_name',
        'contact_phone',
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }
}
