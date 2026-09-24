<?php

namespace natilosir\bot\Bot\Drivers\Telegram\Traits;

trait KeyboardTrait {
    /** @var array<int, array<int, array<string, mixed>>> */
    private array $keyboardRows = [];

    public function clearCache(): static {
        $this->keyboardRows = [];
        return $this;
    }

    public function row( $buttons ): static {
        $this->keyboardRows[] = $buttons;
        return $this;
    }

    public function column( $text, $callback_data = null, $url = null ): array {
        $button = ['text' => $text];

        if ( $callback_data !== null ) {
            $button['callback_data'] = $callback_data;
        }

        if ( $url !== null ) {
            $button['url'] = $url;
        }

        return $button;
    }

    public function inline( $chatID, $second_OR_text, $message_id, $copy = false ) {
        $replyMarkup = $this->encodeKeyboard([
            'inline_keyboard' => $this->consumeKeyboardRows(),
        ]);

        if ( $copy === 'edit' ) {
            return $this->editMessageReplyMarkup($chatID, $message_id, $replyMarkup);
        }

        if ( $copy ) {
            return $this->copyMessage($chatID, $second_OR_text, $message_id, $replyMarkup);
        }

        return $this->sendMessage($chatID, $second_OR_text, $message_id, $replyMarkup);
    }

    public function keyboard( $chatID, $second_OR_text, $message_id, $copy = false, $resize = true, $one_time = false ) {
        $replyMarkup = $this->encodeKeyboard([
            'keyboard'          => $this->consumeKeyboardRows(),
            'resize_keyboard'   => $resize,
            'one_time_keyboard' => $one_time,
        ]);

        if ( $copy ) {
            return $this->copyMessage($chatID, $second_OR_text, $message_id, $replyMarkup);
        }

        return $this->sendMessage($chatID, $second_OR_text, $message_id, $replyMarkup);
    }

    public function forceReply( array $options = [] ): string {
        return $this->encodeKeyboard(['force_reply' => true] + $options);
    }

    public function removeKeyboard( array $options = [] ): string {
        return $this->encodeKeyboard(['remove_keyboard' => true] + $options);
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
