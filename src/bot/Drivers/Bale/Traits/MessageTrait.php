<?php

namespace natilosir\bot\Bot\Drivers\Bale\Traits;

use Illuminate\Support\Arr;

trait MessageTrait {
    public function sendMessage( $chatIdOrData, $text = null, $replyToMessageId = null, $replyMarkup = null ): mixed {
        if ( is_array($chatIdOrData) ) return $this->api('sendMessage', $chatIdOrData);
        $data = [ 'chat_id' => $chatIdOrData, 'text' => $text ];
        Arr::set($data, 'reply_to_message_id', $replyToMessageId);
        Arr::set($data, 'reply_markup', $replyMarkup);
        return $this->api('sendMessage', $data);
    }

    public function forwardMessage( $chatIdOrData, $fromChatId = null, $messageId = null ): mixed {
        if ( is_array($chatIdOrData) ) return $this->api('forwardMessage', $chatIdOrData);
        return $this->api('forwardMessage', [
            'chat_id'      => $chatIdOrData,
            'from_chat_id' => $fromChatId,
            'message_id'   => $messageId,
        ]);
    }

    public function copyMessage( $chatIdOrData, $fromChatId = null, $messageId = null ): mixed {
        if ( is_array($chatIdOrData) ) return $this->api('copyMessage', $chatIdOrData);
        return $this->api('copyMessage', [
            'chat_id'      => $chatIdOrData,
            'from_chat_id' => $fromChatId,
            'message_id'   => $messageId,
        ]);
    }

    public function sendChatAction( $chatIdOrData, $action = null ): mixed {
        if ( is_array($chatIdOrData) ) return $this->api('sendChatAction', $chatIdOrData);
        return $this->api('sendChatAction', [ 'chat_id' => $chatIdOrData, 'action' => $action ]);
    }

    public function answerCallbackQuery( $callbackQueryIdOrData, $text = null, $showAlert = null ): mixed {
        if ( is_array($callbackQueryIdOrData) ) return $this->api('answerCallbackQuery', $callbackQueryIdOrData);
        $data = [ 'callback_query_id' => $callbackQueryIdOrData ];
        Arr::set($data, 'text', $text);
        Arr::set($data, 'show_alert', $showAlert);
        return $this->api('answerCallbackQuery', $data);
    }
}
