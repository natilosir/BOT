<?php

namespace natilosir\bot\Bot\Drivers\Telegram\Traits;

use Illuminate\Support\Arr;

trait MessageTrait {
    // Backward-compatible legacy method.
    public function sendChatAction( $chatID, $action ) {
        $data = [
            'chat_id' => $chatID,
            'action'  => $action,
        ];

        return $this->api('sendChatAction', $data);
    }

    // Backward-compatible legacy method.
    public function forwardMessage( $chatID, $from_chat_id, $message_id ) {
        $data = [
            'chat_id'      => $chatID,
            'from_chat_id' => $from_chat_id,
            'message_id'   => $message_id,
        ];

        return $this->api('forwardMessage', $data);
    }

    // Backward-compatible legacy method.
    public function deleteMessage( $chatID, $message_id ) {
        $data = [
            'chat_id'    => $chatID,
            'message_id' => $message_id,
        ];

        return $this->api('deleteMessage', $data);
    }

    // Backward-compatible legacy method.
    public function copyMessage( $chatID, $second_chat_id, $message_id, $reply_markup = null ) {
        $data = [
            'chat_id'      => $chatID,
            'from_chat_id' => $second_chat_id,
            'message_id'   => $message_id,
        ];

        if ( $reply_markup ) {
            $data['reply_markup'] = $reply_markup;
        }

        return $this->api('copyMessage', $data);
    }

    // Backward-compatible legacy method. The signature and payload behavior are preserved.
    public function sendMessage( $chatID, $text, $reply_to_message_id = null, $reply_markup = null ) {
        $data = [
            'chat_id'    => $chatID,
            'text'       => $text,
            'parse_mode' => 'HTML',
        ];

        if ( $reply_markup ) {
            $data['reply_markup'] = $reply_markup;
        }

        if ( $reply_to_message_id ) {
            $data['reply_to_message_id'] = $reply_to_message_id;
        }

        return $this->api('sendMessage', $data);
    }

    /**
     * Full Telegram Bot API sendMessage wrapper.
     *
     * The previous array-only form is still accepted as the first argument for
     * compatibility with earlier versions of this package.
     */
    public function sendMessageRaw( $chatID, $text = null, $business_connection_id = null, $message_thread_id = null, $direct_messages_topic_id = null, $ephemeral_message_parameters = null, $parse_mode = null, $entities = null, $link_preview_options = null, $disable_notification = null, $protect_content = null, $allow_paid_broadcast = null, $message_effect_id = null, $suggested_post_parameters = null, $reply_parameters = null, $reply_markup = null ) {
        if ( is_array($chatID) && func_num_args() === 1 ) {
            return $this->api('sendMessage', $chatID);
        }

        $data = [
            'chat_id' => $chatID,
            'text'    => $text,
        ];

        Arr::set($data, 'business_connection_id', $business_connection_id);
        Arr::set($data, 'message_thread_id', $message_thread_id);
        Arr::set($data, 'direct_messages_topic_id', $direct_messages_topic_id);
        Arr::set($data, 'ephemeral_message_parameters', $ephemeral_message_parameters);
        Arr::set($data, 'parse_mode', $parse_mode);
        Arr::set($data, 'entities', $entities);
        Arr::set($data, 'link_preview_options', $link_preview_options);
        Arr::set($data, 'disable_notification', $disable_notification);
        Arr::set($data, 'protect_content', $protect_content);
        Arr::set($data, 'allow_paid_broadcast', $allow_paid_broadcast);
        Arr::set($data, 'message_effect_id', $message_effect_id);
        Arr::set($data, 'suggested_post_parameters', $suggested_post_parameters);
        Arr::set($data, 'reply_parameters', $reply_parameters);
        Arr::set($data, 'reply_markup', $reply_markup);

        return $this->api('sendMessage', $data);
    }

    /** Full Telegram Bot API sendChatAction wrapper. */
    public function sendChatActionRaw( $chatID, $action = null, $business_connection_id = null, $message_thread_id = null ) {
        if ( is_array($chatID) && func_num_args() === 1 ) {
            return $this->api('sendChatAction', $chatID);
        }

        $data = [
            'chat_id' => $chatID,
            'action'  => $action,
        ];

        Arr::set($data, 'business_connection_id', $business_connection_id);
        Arr::set($data, 'message_thread_id', $message_thread_id);

        return $this->api('sendChatAction', $data);
    }

    /** Full Telegram Bot API forwardMessage wrapper. */
    public function forwardMessageRaw( $chatID, $from_chat_id = null, $message_id = null, $message_thread_id = null, $direct_messages_topic_id = null, $video_start_timestamp = null, $disable_notification = null, $protect_content = null, $message_effect_id = null, $suggested_post_parameters = null ) {
        if ( is_array($chatID) && func_num_args() === 1 ) {
            return $this->api('forwardMessage', $chatID);
        }

        $data = [
            'chat_id'      => $chatID,
            'from_chat_id' => $from_chat_id,
            'message_id'   => $message_id,
        ];

        Arr::set($data, 'message_thread_id', $message_thread_id);
        Arr::set($data, 'direct_messages_topic_id', $direct_messages_topic_id);
        Arr::set($data, 'video_start_timestamp', $video_start_timestamp);
        Arr::set($data, 'disable_notification', $disable_notification);
        Arr::set($data, 'protect_content', $protect_content);
        Arr::set($data, 'message_effect_id', $message_effect_id);
        Arr::set($data, 'suggested_post_parameters', $suggested_post_parameters);

        return $this->api('forwardMessage', $data);
    }

    /** Full Telegram Bot API forwardMessages wrapper. */
    public function forwardMessages( $chatID, $from_chat_id = null, $message_ids = null, $message_thread_id = null, $direct_messages_topic_id = null, $disable_notification = null, $protect_content = null ) {
        if ( is_array($chatID) && func_num_args() === 1 ) {
            return $this->api('forwardMessages', $chatID);
        }

        $data = [
            'chat_id'      => $chatID,
            'from_chat_id' => $from_chat_id,
            'message_ids'  => $message_ids,
        ];

        Arr::set($data, 'message_thread_id', $message_thread_id);
        Arr::set($data, 'direct_messages_topic_id', $direct_messages_topic_id);
        Arr::set($data, 'disable_notification', $disable_notification);
        Arr::set($data, 'protect_content', $protect_content);

        return $this->api('forwardMessages', $data);
    }

    /** Full Telegram Bot API copyMessage wrapper. */
    public function copyMessageRaw( $chatID, $from_chat_id = null, $message_id = null, $message_thread_id = null, $direct_messages_topic_id = null, $video_start_timestamp = null, $caption = null, $parse_mode = null, $caption_entities = null, $show_caption_above_media = null, $disable_notification = null, $protect_content = null, $allow_paid_broadcast = null, $message_effect_id = null, $suggested_post_parameters = null, $reply_parameters = null, $reply_markup = null ) {
        if ( is_array($chatID) && func_num_args() === 1 ) {
            return $this->api('copyMessage', $chatID);
        }

        $data = [
            'chat_id'      => $chatID,
            'from_chat_id' => $from_chat_id,
            'message_id'   => $message_id,
        ];

        Arr::set($data, 'message_thread_id', $message_thread_id);
        Arr::set($data, 'direct_messages_topic_id', $direct_messages_topic_id);
        Arr::set($data, 'video_start_timestamp', $video_start_timestamp);
        Arr::set($data, 'caption', $caption);
        Arr::set($data, 'parse_mode', $parse_mode);
        Arr::set($data, 'caption_entities', $caption_entities);
        Arr::set($data, 'show_caption_above_media', $show_caption_above_media);
        Arr::set($data, 'disable_notification', $disable_notification);
        Arr::set($data, 'protect_content', $protect_content);
        Arr::set($data, 'allow_paid_broadcast', $allow_paid_broadcast);
        Arr::set($data, 'message_effect_id', $message_effect_id);
        Arr::set($data, 'suggested_post_parameters', $suggested_post_parameters);
        Arr::set($data, 'reply_parameters', $reply_parameters);
        Arr::set($data, 'reply_markup', $reply_markup);

        return $this->api('copyMessage', $data);
    }

    /** Full Telegram Bot API copyMessages wrapper. */
    public function copyMessages( $chatID, $from_chat_id = null, $message_ids = null, $message_thread_id = null, $direct_messages_topic_id = null, $disable_notification = null, $protect_content = null, $remove_caption = null ) {
        if ( is_array($chatID) && func_num_args() === 1 ) {
            return $this->api('copyMessages', $chatID);
        }

        $data = [
            'chat_id'      => $chatID,
            'from_chat_id' => $from_chat_id,
            'message_ids'  => $message_ids,
        ];

        Arr::set($data, 'message_thread_id', $message_thread_id);
        Arr::set($data, 'direct_messages_topic_id', $direct_messages_topic_id);
        Arr::set($data, 'disable_notification', $disable_notification);
        Arr::set($data, 'protect_content', $protect_content);
        Arr::set($data, 'remove_caption', $remove_caption);

        return $this->api('copyMessages', $data);
    }

    /** Full Telegram Bot API deleteMessage wrapper. */
    public function deleteMessageRaw( $chatID, $message_id = null ) {
        if ( is_array($chatID) && func_num_args() === 1 ) {
            return $this->api('deleteMessage', $chatID);
        }

        return $this->api('deleteMessage', [
            'chat_id'    => $chatID,
            'message_id' => $message_id,
        ]);
    }

    /** Full Telegram Bot API deleteMessages wrapper. */
    public function deleteMessages( $chatID, $message_ids = null ) {
        if ( is_array($chatID) && func_num_args() === 1 ) {
            return $this->api('deleteMessages', $chatID);
        }

        return $this->api('deleteMessages', [
            'chat_id'     => $chatID,
            'message_ids' => $message_ids,
        ]);
    }

    /** Full Telegram Bot API sendDice wrapper. */
    public function sendDice( $chatID, $emoji = null, $business_connection_id = null, $message_thread_id = null, $direct_messages_topic_id = null, $disable_notification = null, $protect_content = null, $allow_paid_broadcast = null, $message_effect_id = null, $suggested_post_parameters = null, $reply_parameters = null, $reply_markup = null ) {
        if ( is_array($chatID) && func_num_args() === 1 ) {
            return $this->api('sendDice', $chatID);
        }

        $data = [ 'chat_id' => $chatID ];

        Arr::set($data, 'emoji', $emoji);
        Arr::set($data, 'business_connection_id', $business_connection_id);
        Arr::set($data, 'message_thread_id', $message_thread_id);
        Arr::set($data, 'direct_messages_topic_id', $direct_messages_topic_id);
        Arr::set($data, 'disable_notification', $disable_notification);
        Arr::set($data, 'protect_content', $protect_content);
        Arr::set($data, 'allow_paid_broadcast', $allow_paid_broadcast);
        Arr::set($data, 'message_effect_id', $message_effect_id);
        Arr::set($data, 'suggested_post_parameters', $suggested_post_parameters);
        Arr::set($data, 'reply_parameters', $reply_parameters);
        Arr::set($data, 'reply_markup', $reply_markup);

        return $this->api('sendDice', $data);
    }

    /** Full Telegram Bot API setMessageReaction wrapper. */
    public function setMessageReaction( $chatID, $message_id = null, $reaction = null, $is_big = null ) {
        if ( is_array($chatID) && func_num_args() === 1 ) {
            return $this->api('setMessageReaction', $chatID);
        }

        $data = [
            'chat_id'    => $chatID,
            'message_id' => $message_id,
        ];

        Arr::set($data, 'reaction', $reaction);
        Arr::set($data, 'is_big', $is_big);

        return $this->api('setMessageReaction', $data);
    }

    /** Full Telegram Bot API deleteAllMessageReactions wrapper. */
    public function deleteAllMessageReactions( $chatID, $user_id = null, $actor_chat_id = null ) {
        if ( is_array($chatID) && func_num_args() === 1 ) {
            return $this->api('deleteAllMessageReactions', $chatID);
        }

        $data = [ 'chat_id' => $chatID ];
        Arr::set($data, 'user_id', $user_id);
        Arr::set($data, 'actor_chat_id', $actor_chat_id);

        return $this->api('deleteAllMessageReactions', $data);
    }

    /** Full Telegram Bot API deleteMessageReaction wrapper. */
    public function deleteMessageReaction( $chatID, $message_id = null, $user_id = null, $actor_chat_id = null ) {
        if ( is_array($chatID) && func_num_args() === 1 ) {
            return $this->api('deleteMessageReaction', $chatID);
        }

        $data = [
            'chat_id'    => $chatID,
            'message_id' => $message_id,
        ];

        Arr::set($data, 'user_id', $user_id);
        Arr::set($data, 'actor_chat_id', $actor_chat_id);

        return $this->api('deleteMessageReaction', $data);
    }

    /** Full Telegram Bot API sendChecklist wrapper. */
    public function sendChecklist( $business_connection_id, $chatID = null, $checklist = null, $disable_notification = null, $protect_content = null, $message_effect_id = null, $reply_parameters = null, $reply_markup = null ) {
        if ( is_array($business_connection_id) && func_num_args() === 1 ) {
            return $this->api('sendChecklist', $business_connection_id);
        }

        $data = [
            'business_connection_id' => $business_connection_id,
            'chat_id'                => $chatID,
            'checklist'              => $checklist,
        ];

        Arr::set($data, 'disable_notification', $disable_notification);
        Arr::set($data, 'protect_content', $protect_content);
        Arr::set($data, 'message_effect_id', $message_effect_id);
        Arr::set($data, 'reply_parameters', $reply_parameters);
        Arr::set($data, 'reply_markup', $reply_markup);

        return $this->api('sendChecklist', $data);
    }

    /** Full Telegram Bot API editMessageChecklist wrapper. */
    public function editMessageChecklist( $business_connection_id, $chatID = null, $message_id = null, $checklist = null, $reply_markup = null ) {
        if ( is_array($business_connection_id) && func_num_args() === 1 ) {
            return $this->api('editMessageChecklist', $business_connection_id);
        }

        $data = [
            'business_connection_id' => $business_connection_id,
            'chat_id'                => $chatID,
            'message_id'             => $message_id,
            'checklist'              => $checklist,
        ];

        Arr::set($data, 'reply_markup', $reply_markup);

        return $this->api('editMessageChecklist', $data);
    }
}
