<?php

namespace natilosir\bot\bot\Traits;

trait GuestTrait {
    public function answerGuestQuery( ...$args ) {
        $data = $this->buildApiData($args);
        return $this->api('answerGuestQuery', $data);
    }
}
