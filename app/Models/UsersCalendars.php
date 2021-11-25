<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsersCalendars extends Model
{
    use HasFactory;

    /**
     * @var array
     */
    protected $fillable = [
    	'user_id',
		'calendar_id',
        'permissions',
    ];

    protected $casts = [
    	'user_id' => 'integer',
        'calendar_id' => 'integer',
        'permissions' => 'integer',
    ];
}
