<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tagtranslation extends Model
{
    /**
     * @var array
     */
    protected $fillable = [
        'tag_id',
        'title'
    ];

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function tag()
    {
        return $this->belongsTo('App\Tag', 'tag_id');
    }
}