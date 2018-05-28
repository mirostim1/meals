<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ingtranslation extends Model
{
    /**
     * @var array
     */
    protected $fillable = [
        'ingredient_id',
        'language_id',
        'title'
    ];

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function ingredient()
    {
        return $this->belongsTo('App\Ingredient', 'ingredient_id');
    }
}