<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'cognito_username', 'email',
    ];

    /**
     * Get the lawsuits for the user.
     */
    public function lawsuits()
    {
        return $this->hasMany('App\Models\Lawsuit');
    }
}
