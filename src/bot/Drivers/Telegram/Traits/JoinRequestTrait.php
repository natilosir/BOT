<?php

namespace natilosir\bot\Bot\Drivers\Telegram\Traits;

trait JoinRequestTrait {
    public function answerChatJoinRequestQuery( ...$args ) {
        return $this->apiFromArguments('answerChatJoinRequestQuery', $args);
    }

    public function sendChatJoinRequestWebApp( ...$args ) {
        return $this->apiFromArguments('sendChatJoinRequestWebApp', $args);
    }
}
