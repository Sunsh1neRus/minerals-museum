<?php

namespace App;

use App\Http\Requests\Request;
use Auth;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * App\User
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Mineral[] $minerals
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\MineralsImage[] $mineralsImages
 * @mixin \Eloquent
 * @property-read \App\Role $role
 */
class User extends Authenticatable
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'role_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function minerals()
    {
        return $this->hasMany('App\Mineral', 'user_id', 'id');
    }

    public function role()
    {
        return $this->belongsTo('App\Role', 'role_id', 'id');
    }

    public function mineralsImages()
    {
        return $this->hasMany('App\MineralsImage', 'user_id', 'id');
    }

    public function isAdmin()
    {
        $user_role = $this->role;
        if (!is_null($user_role)) {
            if ($user_role->name == 'admin') {
                return true;
            }
        }

        return false;
    }

    /**
     * @param $param_role string
     * @return bool
     */
    public function is($param_role)
    {
        $user_role = $this->role;
        if (is_null($user_role)) {
            return false;
        }
        $param_role = mb_strtolower($param_role);
        $array_roles = explode('|', $param_role);
        foreach ($array_roles as $v) {
            if (in_array($v, ['admin', 'moderator', 'editor', 'newsman'])) {
                if ($user_role->name == $v) {
                    return true;
                }
            }
        }
        return false;
    }
}
