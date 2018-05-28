<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    /**
     * @var string
     */
    protected $primaryKey = 'language_id';

    /**
     * @var array
     */
    protected $fillable = [
        'language'
    ];

    /**
     * @var bool
     */
    public $timestamps = false;
}
