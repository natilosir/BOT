<?php

namespace natilosir\bot\Bot\Drivers\Telegram\Traits;

trait ForumTrait {
    public function getForumTopicIconStickers( array $data = [] ) {
        return $this->api('getForumTopicIconStickers', $data);
    }

    public function createForumTopic( $chatIdOrData, $name = null, $iconColor = null, $iconCustomEmojiId = null ) {
        if ( is_array($chatIdOrData) ) return $this->api('createForumTopic', $chatIdOrData);
        $data = [ 'chat_id' => $chatIdOrData, 'name' => $name ];
        $this->addOptional($data, 'icon_color', $iconColor);
        $this->addOptional($data, 'icon_custom_emoji_id', $iconCustomEmojiId);
        return $this->api('createForumTopic', $data);
    }

    public function editForumTopic( $chatIdOrData, $messageThreadId = null, $name = null, $iconCustomEmojiId = null ) {
        if ( is_array($chatIdOrData) ) return $this->api('editForumTopic', $chatIdOrData);
        $data = [ 'chat_id' => $chatIdOrData, 'message_thread_id' => $messageThreadId ];
        $this->addOptional($data, 'name', $name);
        $this->addOptional($data, 'icon_custom_emoji_id', $iconCustomEmojiId);
        return $this->api('editForumTopic', $data);
    }

    public function closeForumTopic( $chatIdOrData, $messageThreadId = null ) {
        return $this->forumTopicCall('closeForumTopic', $chatIdOrData, $messageThreadId);
    }

    public function reopenForumTopic( $chatIdOrData, $messageThreadId = null ) {
        return $this->forumTopicCall('reopenForumTopic', $chatIdOrData, $messageThreadId);
    }

    public function deleteForumTopic( $chatIdOrData, $messageThreadId = null ) {
        return $this->forumTopicCall('deleteForumTopic', $chatIdOrData, $messageThreadId);
    }

    public function unpinAllForumTopicMessages( $chatIdOrData, $messageThreadId = null ) {
        return $this->forumTopicCall('unpinAllForumTopicMessages', $chatIdOrData, $messageThreadId);
    }

    protected function forumTopicCall( $method, $chatIdOrData, $messageThreadId = null ) {
        if ( is_array($chatIdOrData) ) return $this->api($method, $chatIdOrData);
        return $this->api($method, [ 'chat_id' => $chatIdOrData, 'message_thread_id' => $messageThreadId ]);
    }

    public function editGeneralForumTopic( $chatIdOrData, $name = null ) {
        if ( is_array($chatIdOrData) ) return $this->api('editGeneralForumTopic', $chatIdOrData);
        return $this->api('editGeneralForumTopic', [ 'chat_id' => $chatIdOrData, 'name' => $name ]);
    }

    public function closeGeneralForumTopic( $chatIdOrData ) {
        return $this->generalForumCall('closeGeneralForumTopic', $chatIdOrData);
    }

    public function reopenGeneralForumTopic( $chatIdOrData ) {
        return $this->generalForumCall('reopenGeneralForumTopic', $chatIdOrData);
    }

    public function hideGeneralForumTopic( $chatIdOrData ) {
        return $this->generalForumCall('hideGeneralForumTopic', $chatIdOrData);
    }

    public function unhideGeneralForumTopic( $chatIdOrData ) {
        return $this->generalForumCall('unhideGeneralForumTopic', $chatIdOrData);
    }

    public function unpinAllGeneralForumTopicMessages( $chatIdOrData ) {
        return $this->generalForumCall('unpinAllGeneralForumTopicMessages', $chatIdOrData);
    }

    protected function generalForumCall( $method, $chatIdOrData ) {
        if ( is_array($chatIdOrData) ) return $this->api($method, $chatIdOrData);
        return $this->api($method, [ 'chat_id' => $chatIdOrData ]);
    }
}
