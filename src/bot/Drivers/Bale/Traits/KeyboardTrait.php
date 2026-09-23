<?php

namespace natilosir\bot\Bot\Drivers\Bale\Traits;

trait KeyboardTrait {
    protected static array $keyboardRows = [];

    public function column( string $text, ?string $callbackData = null, ?string $url = null ): array {
        $button = [ 'text' => $text ];

        if ( $callbackData !== null && $callbackData !== '' ) {
            $button['callback_data'] = $callbackData;
        }

        if ( $url !== null && $url !== '' ) {
            $button['url'] = $url;
        }

        return $button;
    }

    public function row( array $buttons ): static {
        self::$keyboardRows[] = array_values($buttons);
        return $this;
    }

    public function keyboard( $chatID, string $text, $messageID = null, bool $copy = false, bool $resize = true, bool $oneTime = false, bool $edit = false ): mixed {
        $markup = [
            'keyboard'          => self::$keyboardRows,
            'resize_keyboard'   => $resize,
            'one_time_keyboard' => $oneTime,
        ];

        self::$keyboardRows = [];

        $data = [
            'chat_id'      => $chatID,
            'text'         => $text,
            'reply_markup' => json_encode($markup, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        ];

        if ( $edit && $messageID ) {
            $data['message_id'] = $messageID;
            return $this->api('editMessageText', $data);
        }

        return $this->api('sendMessage', $data);
    }

    public function inline( $chatID, string $text, $messageID = null, bool $copy = false, bool $edit = false ): mixed {
        $markup = [
            'inline_keyboard' => self::$keyboardRows,
        ];

        self::$keyboardRows = [];

        $data = [
            'chat_id'      => $chatID,
            'text'         => $text,
            'reply_markup' => json_encode($markup, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        ];

        if ( $edit && $messageID ) {
            $data['message_id'] = $messageID;
            return $this->api('editMessageText', $data);
        }

        return $this->api('sendMessage', $data);
    }
}