<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'subject',
        'message',
        'status',
        'category',
        'priority',
        'summary',
        'suggested_reply',
        'analyzed_at',
    ];

    protected function casts(): array
    {
        return [
            'analyzed_at' => 'datetime',
        ];
    }
}
