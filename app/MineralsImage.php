<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\MineralsImage
 *
 * @property-read \App\User $user
 * @property-read \App\Mineral $mineral
 * @mixin \Eloquent
 */
class MineralsImage extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [
        'id', 'user_id', 'created_at', 'updated_at'
    ];

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }

    public function mineral()
    {
        return $this->belongsTo('App\Mineral', 'mineral_id', 'id');
    }
}
