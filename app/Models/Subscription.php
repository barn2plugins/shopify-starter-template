<?php

namespace Barn2App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'charge_id',
        'plan',
        'price',
        'currency',
        'status',
        'is_active',
        'billing_on',
        'activated_on',
        'trial_ends_on',
        'trial_days',
        'cancelled_on',
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

    /**
     * Check if the subscription is active
     */
    public function isActive(): bool
    {
        return $this->is_active && $this->status === 'active' && $this->cancelled_on === null;
    }
}
