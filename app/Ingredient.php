<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ingredient extends Model
{
    /**
     * @var string
     */
    protected $primaryKey = 'ingredient_id';

    /**
     * @var array
     */
    protected $fillable = [
        'meal_id',
        'slug'
    ];

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function ingtranslations()
    {
        return $this->hasMany('App\Ingtranslation', 'ingredient_id');
    }
}
