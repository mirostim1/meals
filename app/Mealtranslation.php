<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Mealtranslation extends Model
{
    /**
     * @var array
     */
    protected $fillable = [
        'meal_id',
        'language_id',
        'title',
        'description'
    ];

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function meal()
    {
        return $this->belongsTo('App\Meal', 'meal_id');
    }
}
