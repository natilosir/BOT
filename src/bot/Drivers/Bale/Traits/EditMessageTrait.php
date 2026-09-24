<?php

namespace natilosir\bot\Bot\Drivers\Bale\Traits;

use Illuminate\Support\Arr;

trait EditMessageTrait {
    public function editMessageText( $chatIdOrData, $messageId = null, $text = null, $replyMarkup = null ): mixed {
        if ( is_array($chatIdOrData) ) return $this->api('editMessageText', $chatIdOrData);
        $data = [ 'chat_id' => $chatIdOrData, 'message_id' => $messageId, 'text' => $text ];
        Arr::set($data, 'reply_markup', $replyMarkup);
        return $this->api('editMessageText', $data);
    }

    public function editMessageCaption( $chatIdOrData, $messageId = null, $caption = null, $replyMarkup = null ): mixed {
        if ( is_array($chatIdOrData) ) return $this->api('editMessageCaption', $chatIdOrData);
        $data = [ 'chat_id' => $chatIdOrData, 'message_id' => $messageId ];
        Arr::set($data, 'caption', $caption);
        Arr::set($data, 'reply_markup', $replyMarkup);
        return $this->api('editMessageCaption', $data);
    }

    public function editMessageReplyMarkup( $chatIdOrData, $messageId = null, $replyMarkup = null ): mixed {
        if ( is_array($chatIdOrData) ) return $this->api('editMessageReplyMarkup', $chatIdOrData);
        $data = [ 'chat_id' => $chatIdOrData, 'message_id' => $messageId ];
        Arr::set($data, 'reply_markup', $replyMarkup);
        return $this->api('editMessageReplyMarkup', $data);
    }

    public function deleteMessage( $chatIdOrData, $messageId = null ): mixed {
        return $this->chatMessageMethod('deleteMessage', $chatIdOrData, $messageId);
    }
}
