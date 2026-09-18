<?php

namespace natilosir\bot\bot\Traits;

trait PollTrait {
    /**
     * Important parameters are explicit; all advanced Bot API fields can be
     * supplied in $extra (question_entities, media, correct_option_ids,
     * explanation_media, close_date, members_only, country_codes, etc.).
     */
    public function sendPoll( $chatIdOrData, $question = null, $options = null, array $extra = [] ) {
        if ( is_array($chatIdOrData) ) return $this->api('sendPoll', $chatIdOrData);
        return $this->api('sendPoll', $this->withExtra([
            'chat_id'  => $chatIdOrData,
            'question' => $question,
            'options'  => $options,
        ], $extra));
    }

    public function stopPoll( $chatIdOrData, $messageId = null, $replyMarkup = null, $businessConnectionId = null ) {
        if ( is_array($chatIdOrData) ) return $this->api('stopPoll', $chatIdOrData);
        $data = [ 'chat_id' => $chatIdOrData, 'message_id' => $messageId ];
        $this->addOptional($data, 'reply_markup', $replyMarkup);
        $this->addOptional($data, 'business_connection_id', $businessConnectionId);
        return $this->api('stopPoll', $data);
    }
}
