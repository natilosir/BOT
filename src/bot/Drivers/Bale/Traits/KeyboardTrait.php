<?php

namespace natilosir\bot\Bot\Drivers\Bale\Traits;

trait KeyboardTrait {
    /** @var array<int, array<int, array<string, mixed>>> */
    private array $keyboardRows = [];

    public function clearCache(): static {
        $this->keyboardRows = [];
        return $this;
    }

    public function column( string $text, ?string $callbackData = null, ?string $url = null ): array {
        $button = ['text' => $text];

        if ( $callbackData !== null && $callbackData !== '' ) {
            $button['callback_data'] = $callbackData;
        }

        if ( $url !== null && $url !== '' ) {
            $button['url'] = $url;
        }

        return $button;
    }

    public function row( array $buttons ): static {
        $this->keyboardRows[] = array_values($buttons);
        return $this;
    }

    public function keyboard( $chatID, string $text, $messageID = null, bool $copy = false, bool $resize = true, bool $oneTime = false, bool $edit = false ): mixed {
        $data = [
            'chat_id'      => $chatID,
            'text'         => $text,
            'reply_markup' => $this->encodeKeyboard([
                'keyboard'          => $this->consumeKeyboardRows(),
                'resize_keyboard'   => $resize,
                'one_time_keyboard' => $oneTime,
            ]),
        ];

        if ( $edit && $messageID ) {
            $data['message_id'] = $messageID;
            return $this->api('editMessageText', $data);
        }

        return $this->api('sendMessage', $data);
    }

    public function inline( $chatID, string $text, $messageID = null, bool $copy = false, bool $edit = false ): mixed {
        $data = [
            'chat_id'      => $chatID,
            'text'         => $text,
            'reply_markup' => $this->encodeKeyboard([
                'inline_keyboard' => $this->consumeKeyboardRows(),
            ]),
        ];

        if ( $edit && $messageID ) {
            $data['message_id'] = $messageID;
            return $this->api('editMessageText', $data);
        }

        return $this->api('sendMessage', $data);
    }

    private function consumeKeyboardRows(): array {
        $rows = $this->keyboardRows;
        $this->keyboardRows = [];

        return $rows;
    }

    private function encodeKeyboard( array $markup ): string {
        return (string) json_encode($markup, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}
