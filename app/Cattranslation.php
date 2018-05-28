<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cattranslation extends Model
{
    /**
     * @var array
     */
    protected $fillable = [
        'category_id',
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
    public function category()
    {
        return $this->belongsTo('App\Category', 'category_id');
    }
}
