<?php

namespace natilosir\bot\Bot\Drivers\Telegram\Traits;

trait GuestTrait {
    public function answerGuestQuery( ...$args ) {
        return $this->apiFromArguments('answerGuestQuery', $args);
    }
}
