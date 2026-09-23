<?php

namespace natilosir\bot\Bot\Drivers\Telegram\Traits;

trait DirectMessageTrait {
    /**
     * Approve a suggested post.
     *
     * Backward compatible:
     *   approveSuggestedPost(['chat_id' => 1, 'message_id' => 2])
     * Detailed:
     *   approveSuggestedPost($chatId, $messageId, $sendDate)
     */
    public function approveSuggestedPost( $chatIdOrData, $messageId = null, $sendDate = null ) {
        if ( is_array($chatIdOrData) ) {
            return $this->api('approveSuggestedPost', $chatIdOrData);
        }

        $data = [
            'chat_id'    => $chatIdOrData,
            'message_id' => $messageId,
        ];
        $this->addOptional($data, 'send_date', $sendDate);

        return $this->api('approveSuggestedPost', $data);
    }

    /**
     * Decline a suggested post.
     * comment is optional and may contain up to 128 characters.
     */
    public function declineSuggestedPost( $chatIdOrData, $messageId = null, $comment = null ) {
        if ( is_array($chatIdOrData) ) {
            return $this->api('declineSuggestedPost', $chatIdOrData);
        }

        $data = [
            'chat_id'    => $chatIdOrData,
            'message_id' => $messageId,
        ];
        $this->addOptional($data, 'comment', $comment);

        return $this->api('declineSuggestedPost', $data);
    }
}
