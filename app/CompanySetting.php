<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CompanySetting extends Model
{
    protected $guarded = [];

    public function serviceBookings()
    {
        return $this->belongsTo('App\ServiceBooking');
    }

    public function getEffectiveGeminiKey(): ?string
    {
        return !empty($this->gemini_api_key) ? $this->gemini_api_key : env('GEMINI_API_KEY');
    }
}
