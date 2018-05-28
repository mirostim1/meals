<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    /**
     * @var string
     */
    protected $primaryKey = 'tag_id';

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
    public function tagtranslations()
    {
        return $this->hasMany('App\Tagtranslation', 'tag_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function meal()
    {
        return $this->belongsTo('App\Meal', 'meal_id');
    }
}
