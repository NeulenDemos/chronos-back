<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsersEvents extends Model
{
    use HasFactory;

    /**
     * @var array
     */
    protected $fillable = [
    	'user_id',
		'event_id',
    ];

    protected $casts = [
    	'user_id' => 'integer',
        'event_id' => 'integer',
    ];
}
