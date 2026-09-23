<?php

namespace natilosir\bot\Bot\Drivers\Telegram\Traits;

trait GuestTrait {
    public function answerGuestQuery( ...$args ) {
        $data = $this->buildApiData($args);
        return $this->api('answerGuestQuery', $data);
    }
}
