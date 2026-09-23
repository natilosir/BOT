<?php

namespace natilosir\bot\Bot\Drivers\Telegram\Traits;

trait EphemeralTrait {
    public function editEphemeralMessageText( ...$args ) {
        $data = $this->buildApiData($args);
        return $this->api('editEphemeralMessageText', $data);
    }

    public function editEphemeralMessageMedia( ...$args ) {
        $data = $this->buildApiData($args);
        return $this->api('editEphemeralMessageMedia', $data);
    }

    public function editEphemeralMessageCaption( ...$args ) {
        $data = $this->buildApiData($args);
        return $this->api('editEphemeralMessageCaption', $data);
    }

    public function editEphemeralMessageReplyMarkup( ...$args ) {
        $data = $this->buildApiData($args);
        return $this->api('editEphemeralMessageReplyMarkup', $data);
    }

    public function deleteEphemeralMessage( ...$args ) {
        $data = $this->buildApiData($args);
        return $this->api('deleteEphemeralMessage', $data);
    }
}
