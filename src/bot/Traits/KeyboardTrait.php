<?php

namespace natilosir\bot\bot\Traits;

trait KeyboardTrait {
    private array $keyboard = [];

    public function clearCache() {
        $this->keyboard = [];
        return $this;
    }

    public function row( $buttons ) {
        $this->keyboard[] = $buttons;
        return $this;
    }

    public function column( $text, $callback_data = null, $url = null ) {
        $button = [ 'text' => $text ];

        if ( $callback_data !== null ) {
            $button['callback_data'] = $callback_data;
        }

        if ( $url !== null ) {
            $button['url'] = $url;
        }

        return $button;
    }

    public function inline( $chatID, $second_OR_text, $message_id, $copy = false ) {
        $reply_markup = json_encode([
            'inline_keyboard' => $this->keyboard,
        ]);

        if ( $copy === 'edit' ) {
            return $this->editMessageReplyMarkup($chatID, $message_id, $reply_markup);
        }

        if ( $copy ) {
            return $this->copyMessage($chatID, $second_OR_text, $message_id, $reply_markup);
        }

        return $this->sendMessage($chatID, $second_OR_text, $message_id, $reply_markup);
    }

    public function keyboard( $chatID, $second_OR_text, $message_id, $copy = false, $resize = true, $one_time = false ) {
        $reply_markup = [
            'keyboard'          => $this->keyboard,
            'resize_keyboard'   => $resize,
            'one_time_keyboard' => $one_time,
        ];

        $reply_markup = json_encode($reply_markup);

        if ( $copy ) {
            return $this->copyMessage($chatID, $second_OR_text, $message_id, $reply_markup);
        }

        return $this->sendMessage($chatID, $second_OR_text, $message_id, $reply_markup);
    }

    public function forceReply( array $options = [] ): string {
        return json_encode(array_merge([ 'force_reply' => true ], $options));
    }

    public function removeKeyboard( array $options = [] ): string {
        return json_encode(array_merge([ 'remove_keyboard' => true ], $options));
    }
}
