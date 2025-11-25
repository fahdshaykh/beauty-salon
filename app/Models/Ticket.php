<?php

namespace App\Models;

use App\Company;
use App\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'company_id', 'ticket_id', 'subject', 'description',
        'status', 'priority', 'assigned_to'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function replies()
    {
        return $this->hasMany(TicketReply::class)->orderBy('created_at');
    }

    public function latestReply()
    {
        return $this->hasOne(TicketReply::class)->latestOfMany();
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($ticket) {
            $latest = Ticket::where('company_id', $ticket->company_id)
                ->latest()
                ->first();

            $number = $latest ? (intval(substr($latest->ticket_id, -4)) + 1) : 1;
            $ticket->ticket_id = 'TICKET-' . date('Y') . '-' . str_pad($number, 4, '0', STR_PAD_LEFT);
        });
    }
}
