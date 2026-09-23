<?php

namespace natilosir\bot\Bot\Drivers\Telegram\Traits;

trait JoinRequestTrait {
    public function answerChatJoinRequestQuery( ...$args ) {
        $data = $this->buildApiData($args);
        return $this->api('answerChatJoinRequestQuery', $data);
    }

    public function sendChatJoinRequestWebApp( ...$args ) {
        $data = $this->buildApiData($args);
        return $this->api('sendChatJoinRequestWebApp', $data);
    }
}
