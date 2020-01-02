<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TypeLawsuit extends Model
{
    public $fillable = ['name', 'description'];

    /**
     * Get all lawsuit for type lawsuit.
     */
    public function lawsuits()
    {
        return $this->hasMany('App\Models\Lawsuit');
    }
}
