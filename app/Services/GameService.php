<?php

namespace App\Services;

use App\Models\Game;
use App\Models\Player;

class GameService
{
    public function rollDice()
    {
        return rand(1, 6);
    }

    public function movePlayer($playerId, $diceRoll)
    {
        $player = Player::find($playerId);
        if ($player) {
            $player->position += $diceRoll;
            $player->save();
        }
        return $player;
    }
}
