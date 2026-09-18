<?php

namespace natilosir\bot\bot\Traits;

trait EditMessageTrait {
    // Backward-compatible legacy method.
    public function editMessageReplyMarkup( $chatID, $message_id, $reply_markup = null ) {
        $data = [
            'chat_id'    => $chatID,
            'message_id' => $message_id,
        ];

        if ( $reply_markup ) {
            $data['reply_markup'] = $reply_markup;
        }

        return $this->client()
            ->api('editMessageReplyMarkup', $data);
    }

    public function editMessageText( ...$args ) {
        $data = $this->buildApiData($args);
        return $this->api('editMessageText', $data);
    }

    public function editMessageCaption( ...$args ) {
        $data = $this->buildApiData($args);
        return $this->api('editMessageCaption', $data);
    }

    public function editMessageMedia( ...$args ) {
        $data = $this->buildApiData($args);
        return $this->api('editMessageMedia', $data);
    }

    public function editMessageReplyMarkupRaw( ...$args ) {
        $data = $this->buildApiData($args);
        return $this->api('editMessageReplyMarkup', $data);
    }
}
