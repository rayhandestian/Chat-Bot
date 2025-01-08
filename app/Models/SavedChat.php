<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SavedChat extends Model
{
    protected $fillable = ['title', 'messages'];

    protected $casts = [
        'messages' => 'array'
    ];
} 