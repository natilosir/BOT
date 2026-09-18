<?php

namespace natilosir\bot\bot\Traits;

trait InlineTrait {
    public function answerInlineQuery( $inlineQueryIdOrData, $results = null, $cacheTime = null, $isPersonal = null, $nextOffset = null, $button = null ) {
        if ( is_array($inlineQueryIdOrData) ) return $this->api('answerInlineQuery', $inlineQueryIdOrData);
        $data = [ 'inline_query_id' => $inlineQueryIdOrData, 'results' => $results ];
        $this->addOptional($data, 'cache_time', $cacheTime);
        $this->addOptional($data, 'is_personal', $isPersonal);
        $this->addOptional($data, 'next_offset', $nextOffset);
        $this->addOptional($data, 'button', $button);
        return $this->api('answerInlineQuery', $data);
    }

    public function answerWebAppQuery( $webAppQueryIdOrData, $result = null ) {
        if ( is_array($webAppQueryIdOrData) ) return $this->api('answerWebAppQuery', $webAppQueryIdOrData);
        return $this->api('answerWebAppQuery', [ 'web_app_query_id' => $webAppQueryIdOrData, 'result' => $result ]);
    }

    public function savePreparedInlineMessage( ...$args ) {
        $data = $this->buildApiData($args);
        return $this->api('savePreparedInlineMessage', $data);
    }

    public function savePreparedKeyboardButton( ...$args ) {
        $data = $this->buildApiData($args);
        return $this->api('savePreparedKeyboardButton', $data);
    }
}
