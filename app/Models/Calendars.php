<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Calendars extends Model
{
    use HasFactory;

    /**
     * @var array
     */
    protected $fillable = [
		'title',
        'description',
        'primary',
        'hidden',
	];

    protected $casts = [
		'title' => 'string',
        'description' => 'string',
        'primary' => 'integer',
        'hidden' => 'integer',
    ];
}
