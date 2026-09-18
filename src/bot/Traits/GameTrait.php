<?php

namespace natilosir\bot\bot\Traits;

trait GameTrait {
    public function sendGame( ...$args ) {
        $data = $this->buildApiData($args);
        return $this->api('sendGame', $data);
    }

    public function setGameScore( ...$args ) {
        $data = $this->buildApiData($args);
        return $this->api('setGameScore', $data);
    }

    public function getGameHighScores( ...$args ) {
        $data = $this->buildApiData($args);
        return $this->api('getGameHighScores', $data);
    }
}
