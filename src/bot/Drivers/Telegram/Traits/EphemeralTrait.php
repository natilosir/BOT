<?php

namespace natilosir\bot\Bot\Drivers\Telegram\Traits;

trait EphemeralTrait {
    public function editEphemeralMessageText( ...$args ) {
        return $this->apiFromArguments('editEphemeralMessageText', $args);
    }

    public function editEphemeralMessageMedia( ...$args ) {
        return $this->apiFromArguments('editEphemeralMessageMedia', $args);
    }

    public function editEphemeralMessageCaption( ...$args ) {
        return $this->apiFromArguments('editEphemeralMessageCaption', $args);
    }

    public function editEphemeralMessageReplyMarkup( ...$args ) {
        return $this->apiFromArguments('editEphemeralMessageReplyMarkup', $args);
    }

    public function deleteEphemeralMessage( ...$args ) {
        return $this->apiFromArguments('deleteEphemeralMessage', $args);
    }
}
