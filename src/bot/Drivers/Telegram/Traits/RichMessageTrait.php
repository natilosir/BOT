<?php

namespace natilosir\bot\Bot\Drivers\Telegram\Traits;

trait RichMessageTrait {
    public function sendMessageDraft( ...$args ) {
        $data = $this->buildApiData($args);
        return $this->api('sendMessageDraft', $data);
    }

    public function sendRichMessage( ...$args ) {
        $data = $this->buildApiData($args);
        return $this->api('sendRichMessage', $data);
    }

    public function sendRichMessageDraft( ...$args ) {
        $data = $this->buildApiData($args);
        return $this->api('sendRichMessageDraft', $data);
    }
}
