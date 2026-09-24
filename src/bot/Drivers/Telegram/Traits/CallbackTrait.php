<?php

namespace natilosir\bot\Bot\Drivers\Telegram\Traits;

use Illuminate\Support\Arr;

trait CallbackTrait {
    // Backward-compatible legacy method. Do not change its payload behavior.
    public function alert( $query_id, $text, $show_alert = false ) {
        $data = [
            'callback_query_id' => $query_id,
            'text'              => $text,
            'show_alert'        => $show_alert ? 'true' : 'false',
        ];

        return $this->api('answerCallbackQuery', $data);
    }

    public function answerCallbackQuery( $callbackQueryIdOrData, $text = null, $showAlert = null, $url = null, $cacheTime = null ) {
        if ( is_array($callbackQueryIdOrData) ) {
            return $this->api('answerCallbackQuery', $callbackQueryIdOrData);
        }

        $data = [ 'callback_query_id' => $callbackQueryIdOrData ];
        Arr::set($data, 'text', $text);
        Arr::set($data, 'show_alert', $showAlert);
        Arr::set($data, 'url', $url);
        Arr::set($data, 'cache_time', $cacheTime);

        return $this->api('answerCallbackQuery', $data);
    }
}
