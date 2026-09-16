<?php

namespace App\Models;

use App\Enums\EmailStatus;
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
        'email_subject',
        'email_body',
        'email_status',
        'email_generated_at',
        'email_approved_at',
        'email_sent_at',
        'email_error',
    ];

    protected function casts(): array
    {
        return [
            'analyzed_at' => 'datetime',
            'email_status' => EmailStatus::class,
            'email_generated_at' => 'datetime',
            'email_approved_at' => 'datetime',
            'email_sent_at' => 'datetime',
        ];
    }
}
