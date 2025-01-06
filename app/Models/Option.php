<?php

namespace Barn2App\Models;

use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'value',
    ];
}
