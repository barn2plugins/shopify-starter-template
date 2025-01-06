<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\SoftDeletes;

trait ShopModel
{
    use SoftDeletes;

    public function getDomain()
    {
        return $this->name;
    }

    public function getAccessToken()
    {
        return $this->password;
    }
}
