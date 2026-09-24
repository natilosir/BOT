<?php

namespace natilosir\bot\Bot\Drivers\Telegram\Traits;

trait GameTrait {
    public function sendGame( ...$args ) {
        return $this->apiFromArguments('sendGame', $args);
    }

    public function setGameScore( ...$args ) {
        return $this->apiFromArguments('setGameScore', $args);
    }

    public function getGameHighScores( ...$args ) {
        return $this->apiFromArguments('getGameHighScores', $args);
    }
}
