<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Mineral
 *
 * @property-read \App\User $user
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\MineralsImage[] $mineralsImages
 * @mixin \Eloquent
 */
class Mineral extends Model
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

    public function lastUpdater()
    {
        return $this->belongsTo('App\User', 'last_updater_id', 'id');
    }

    public function mineralsImages()
    {
        return $this->hasMany('App\MineralsImage', 'mineral_id', 'id');
    }
}
