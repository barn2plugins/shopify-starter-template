<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'owner_name',
        'plan_display_name',
        'is_partner_development',
        'is_shopify_plus',
        'currency',
        'timezone',
        'iana_timezone',
        'money_format',
        'money_with_currency_format',
        'money_in_emails_format',
        'money_with_currency_in_emails_format',
        'checkout_api_supported',
    ];

    /**
     * Get the user that owns the subscription
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
