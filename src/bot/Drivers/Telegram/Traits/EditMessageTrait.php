<?php

namespace natilosir\bot\Bot\Drivers\Telegram\Traits;

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

        return $this->api('editMessageReplyMarkup', $data);
    }

    public function editMessageText( ...$args ) {
        return $this->apiFromArguments('editMessageText', $args);
    }

    public function editMessageCaption( ...$args ) {
        return $this->apiFromArguments('editMessageCaption', $args);
    }

    public function editMessageMedia( ...$args ) {
        return $this->apiFromArguments('editMessageMedia', $args);
    }

    public function editMessageReplyMarkupRaw( ...$args ) {
        return $this->apiFromArguments('editMessageReplyMarkup', $args);
    }
}
