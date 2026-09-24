<?php

namespace natilosir\bot\Bot\Drivers\Telegram\Traits;

use Illuminate\Support\Arr;

trait InlineTrait {
    public function answerInlineQuery( $inlineQueryIdOrData, $results = null, $cacheTime = null, $isPersonal = null, $nextOffset = null, $button = null ) {
        if ( is_array($inlineQueryIdOrData) ) return $this->api('answerInlineQuery', $inlineQueryIdOrData);
        $data = [ 'inline_query_id' => $inlineQueryIdOrData, 'results' => $results ];
        Arr::set($data, 'cache_time', $cacheTime);
        Arr::set($data, 'is_personal', $isPersonal);
        Arr::set($data, 'next_offset', $nextOffset);
        Arr::set($data, 'button', $button);
        return $this->api('answerInlineQuery', $data);
    }

    public function answerWebAppQuery( $webAppQueryIdOrData, $result = null ) {
        if ( is_array($webAppQueryIdOrData) ) return $this->api('answerWebAppQuery', $webAppQueryIdOrData);
        return $this->api('answerWebAppQuery', [ 'web_app_query_id' => $webAppQueryIdOrData, 'result' => $result ]);
    }

    public function savePreparedInlineMessage( ...$args ) {
        return $this->apiFromArguments('savePreparedInlineMessage', $args);
    }

    public function savePreparedKeyboardButton( ...$args ) {
        return $this->apiFromArguments('savePreparedKeyboardButton', $args);
    }
}
