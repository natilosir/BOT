<?php

namespace natilosir\bot\Bot\Drivers\Telegram\Traits;

trait RichMessageTrait {
    public function sendMessageDraft( ...$args ) {
        return $this->apiFromArguments('sendMessageDraft', $args);
    }

    public function sendRichMessage( ...$args ) {
        return $this->apiFromArguments('sendRichMessage', $args);
    }

    public function sendRichMessageDraft( ...$args ) {
        return $this->apiFromArguments('sendRichMessageDraft', $args);
    }
}
