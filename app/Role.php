<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Role
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\UsersRole[] $user
 * @mixin \Eloquent
 */
class Role extends Model
{
    //


    public function user()
    {
        return $this->hasMany('App\User', 'role_id', 'id');
    }
}
