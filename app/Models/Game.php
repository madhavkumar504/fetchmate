<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    use HasFactory;

    protected $fillable = ['game_code', 'players', 'board_state', 'current_turn'];

    protected $casts = [
        'players' => 'array',
        'board_state' => 'array',
    ];
}
