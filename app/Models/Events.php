<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Events extends Model
{
    use HasFactory;

    /**
     * @var array
     */
    protected $fillable = [
		'title',
        'calendar_id',
        'description',
        'type',
        'start_dt',
        'end_dt',
        'all_day',
        'color',
	];

    protected $casts = [
		'title' => 'string',
        'calendar_id' => 'integer',
        'description' => 'string',
        'type' => 'string',
        'start_dt' => 'timestamp',
        'end_dt' => 'timestamp',
        'all_day' => 'integer',
        'color' => 'string',
    ];
}
