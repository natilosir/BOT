<?php

namespace natilosir\bot\Bot\Drivers\Telegram\Traits;

trait ChatManagementTrait {
    public function banChatMember( $chatIdOrData, $userId = null, $untilDate = null, $revokeMessages = null ) {
        if ( is_array($chatIdOrData) ) return $this->api('banChatMember', $chatIdOrData);
        $data = [ 'chat_id' => $chatIdOrData, 'user_id' => $userId ];
        $this->addOptional($data, 'until_date', $untilDate);
        $this->addOptional($data, 'revoke_messages', $revokeMessages);
        return $this->api('banChatMember', $data);
    }

    public function unbanChatMember( $chatIdOrData, $userId = null, $onlyIfBanned = null ) {
        if ( is_array($chatIdOrData) ) return $this->api('unbanChatMember', $chatIdOrData);
        $data = [ 'chat_id' => $chatIdOrData, 'user_id' => $userId ];
        $this->addOptional($data, 'only_if_banned', $onlyIfBanned);
        return $this->api('unbanChatMember', $data);
    }

    public function restrictChatMember( $chatIdOrData, $userId = null, $permissions = null, $useIndependentChatPermissions = null, $untilDate = null ) {
        if ( is_array($chatIdOrData) ) return $this->api('restrictChatMember', $chatIdOrData);
        $data = [
            'chat_id'     => $chatIdOrData,
            'user_id'     => $userId,
            'permissions' => $permissions,
        ];
        $this->addOptional($data, 'use_independent_chat_permissions', $useIndependentChatPermissions);
        $this->addOptional($data, 'until_date', $untilDate);
        return $this->api('restrictChatMember', $data);
    }

    /**
     * $rights accepts Telegram promoteChatMember permission keys, e.g.
     * can_manage_chat, can_delete_messages, can_manage_video_chats,
     * can_restrict_members, can_promote_members, can_change_info,
     * can_invite_users, can_post_stories, can_edit_stories,
     * can_delete_stories, can_post_messages, can_edit_messages,
     * can_pin_messages, can_manage_topics, can_manage_direct_messages,
     * can_manage_tags, can_send_welcome_messages.
     */
    public function promoteChatMember( $chatIdOrData, $userId = null, array $rights = [] ) {
        if ( is_array($chatIdOrData) ) return $this->api('promoteChatMember', $chatIdOrData);
        return $this->api('promoteChatMember', array_merge([
            'chat_id' => $chatIdOrData,
            'user_id' => $userId,
        ], $rights));
    }

    public function setChatAdministratorCustomTitle( $chatIdOrData, $userId = null, $customTitle = null ) {
        if ( is_array($chatIdOrData) ) return $this->api('setChatAdministratorCustomTitle', $chatIdOrData);
        return $this->api('setChatAdministratorCustomTitle', [
            'chat_id'      => $chatIdOrData,
            'user_id'      => $userId,
            'custom_title' => $customTitle,
        ]);
    }

    public function setChatMemberTag( $chatIdOrData, $userId = null, $tag = null ) {
        if ( is_array($chatIdOrData) ) return $this->api('setChatMemberTag', $chatIdOrData);
        return $this->api('setChatMemberTag', [
            'chat_id' => $chatIdOrData,
            'user_id' => $userId,
            'tag'     => $tag,
        ]);
    }

    public function banChatSenderChat( $chatIdOrData, $senderChatId = null ) {
        if ( is_array($chatIdOrData) ) return $this->api('banChatSenderChat', $chatIdOrData);
        return $this->api('banChatSenderChat', [ 'chat_id' => $chatIdOrData, 'sender_chat_id' => $senderChatId ]);
    }

    public function unbanChatSenderChat( $chatIdOrData, $senderChatId = null ) {
        if ( is_array($chatIdOrData) ) return $this->api('unbanChatSenderChat', $chatIdOrData);
        return $this->api('unbanChatSenderChat', [ 'chat_id' => $chatIdOrData, 'sender_chat_id' => $senderChatId ]);
    }

    public function setChatPermissions( $chatIdOrData, $permissions = null, $useIndependentChatPermissions = null ) {
        if ( is_array($chatIdOrData) ) return $this->api('setChatPermissions', $chatIdOrData);
        $data = [ 'chat_id' => $chatIdOrData, 'permissions' => $permissions ];
        $this->addOptional($data, 'use_independent_chat_permissions', $useIndependentChatPermissions);
        return $this->api('setChatPermissions', $data);
    }

    public function exportChatInviteLink( $chatIdOrData ) {
        if ( is_array($chatIdOrData) ) return $this->api('exportChatInviteLink', $chatIdOrData);
        return $this->api('exportChatInviteLink', [ 'chat_id' => $chatIdOrData ]);
    }

    public function createChatInviteLink( $chatIdOrData, $name = null, $expireDate = null, $memberLimit = null, $createsJoinRequest = null ) {
        if ( is_array($chatIdOrData) ) return $this->api('createChatInviteLink', $chatIdOrData);
        $data = [ 'chat_id' => $chatIdOrData ];
        $this->addOptional($data, 'name', $name);
        $this->addOptional($data, 'expire_date', $expireDate);
        $this->addOptional($data, 'member_limit', $memberLimit);
        $this->addOptional($data, 'creates_join_request', $createsJoinRequest);
        return $this->api('createChatInviteLink', $data);
    }

    public function editChatInviteLink( $chatIdOrData, $inviteLink = null, $name = null, $expireDate = null, $memberLimit = null, $createsJoinRequest = null ) {
        if ( is_array($chatIdOrData) ) return $this->api('editChatInviteLink', $chatIdOrData);
        $data = [ 'chat_id' => $chatIdOrData, 'invite_link' => $inviteLink ];
        $this->addOptional($data, 'name', $name);
        $this->addOptional($data, 'expire_date', $expireDate);
        $this->addOptional($data, 'member_limit', $memberLimit);
        $this->addOptional($data, 'creates_join_request', $createsJoinRequest);
        return $this->api('editChatInviteLink', $data);
    }

    public function createChatSubscriptionInviteLink( $chatIdOrData, $name = null, $subscriptionPeriod = null, $subscriptionPrice = null ) {
        if ( is_array($chatIdOrData) ) return $this->api('createChatSubscriptionInviteLink', $chatIdOrData);
        $data = [
            'chat_id'             => $chatIdOrData,
            'subscription_period' => $subscriptionPeriod,
            'subscription_price'  => $subscriptionPrice,
        ];
        $this->addOptional($data, 'name', $name);
        return $this->api('createChatSubscriptionInviteLink', $data);
    }

    public function editChatSubscriptionInviteLink( $chatIdOrData, $inviteLink = null, $name = null ) {
        if ( is_array($chatIdOrData) ) return $this->api('editChatSubscriptionInviteLink', $chatIdOrData);
        $data = [ 'chat_id' => $chatIdOrData, 'invite_link' => $inviteLink ];
        $this->addOptional($data, 'name', $name);
        return $this->api('editChatSubscriptionInviteLink', $data);
    }

    public function revokeChatInviteLink( $chatIdOrData, $inviteLink = null ) {
        if ( is_array($chatIdOrData) ) return $this->api('revokeChatInviteLink', $chatIdOrData);
        return $this->api('revokeChatInviteLink', [ 'chat_id' => $chatIdOrData, 'invite_link' => $inviteLink ]);
    }

    public function approveChatJoinRequest( $chatIdOrData, $userId = null ) {
        if ( is_array($chatIdOrData) ) return $this->api('approveChatJoinRequest', $chatIdOrData);
        return $this->api('approveChatJoinRequest', [ 'chat_id' => $chatIdOrData, 'user_id' => $userId ]);
    }

    public function declineChatJoinRequest( $chatIdOrData, $userId = null ) {
        if ( is_array($chatIdOrData) ) return $this->api('declineChatJoinRequest', $chatIdOrData);
        return $this->api('declineChatJoinRequest', [ 'chat_id' => $chatIdOrData, 'user_id' => $userId ]);
    }

    public function setChatPhoto( $chatIdOrData, $photo = null ) {
        if ( is_array($chatIdOrData) ) return $this->api('setChatPhoto', $chatIdOrData);
        return $this->api('setChatPhoto', [ 'chat_id' => $chatIdOrData, 'photo' => $photo ]);
    }

    public function deleteChatPhoto( $chatIdOrData ) {
        if ( is_array($chatIdOrData) ) return $this->api('deleteChatPhoto', $chatIdOrData);
        return $this->api('deleteChatPhoto', [ 'chat_id' => $chatIdOrData ]);
    }

    public function setChatTitle( $chatIdOrData, $title = null ) {
        if ( is_array($chatIdOrData) ) return $this->api('setChatTitle', $chatIdOrData);
        return $this->api('setChatTitle', [ 'chat_id' => $chatIdOrData, 'title' => $title ]);
    }

    public function setChatDescription( $chatIdOrData, $description = null ) {
        if ( is_array($chatIdOrData) ) return $this->api('setChatDescription', $chatIdOrData);
        return $this->api('setChatDescription', [ 'chat_id' => $chatIdOrData, 'description' => $description ]);
    }

    public function pinChatMessage( $chatIdOrData, $messageId = null, $disableNotification = null, $businessConnectionId = null ) {
        if ( is_array($chatIdOrData) ) return $this->api('pinChatMessage', $chatIdOrData);
        $data = [ 'chat_id' => $chatIdOrData, 'message_id' => $messageId ];
        $this->addOptional($data, 'disable_notification', $disableNotification);
        $this->addOptional($data, 'business_connection_id', $businessConnectionId);
        return $this->api('pinChatMessage', $data);
    }

    public function unpinChatMessage( $chatIdOrData, $messageId = null, $businessConnectionId = null ) {
        if ( is_array($chatIdOrData) ) return $this->api('unpinChatMessage', $chatIdOrData);
        $data = [ 'chat_id' => $chatIdOrData ];
        $this->addOptional($data, 'message_id', $messageId);
        $this->addOptional($data, 'business_connection_id', $businessConnectionId);
        return $this->api('unpinChatMessage', $data);
    }

    public function unpinAllChatMessages( $chatIdOrData ) {
        if ( is_array($chatIdOrData) ) return $this->api('unpinAllChatMessages', $chatIdOrData);
        return $this->api('unpinAllChatMessages', [ 'chat_id' => $chatIdOrData ]);
    }

    public function leaveChat( $chatIdOrData ) {
        if ( is_array($chatIdOrData) ) return $this->api('leaveChat', $chatIdOrData);
        return $this->api('leaveChat', [ 'chat_id' => $chatIdOrData ]);
    }

    public function getChat( $chatIdOrData ) {
        if ( is_array($chatIdOrData) ) return $this->api('getChat', $chatIdOrData);
        return $this->api('getChat', [ 'chat_id' => $chatIdOrData ]);
    }

    public function getChatAdministrators( $chatIdOrData ) {
        if ( is_array($chatIdOrData) ) return $this->api('getChatAdministrators', $chatIdOrData);
        return $this->api('getChatAdministrators', [ 'chat_id' => $chatIdOrData ]);
    }

    public function getChatMemberCount( $chatIdOrData ) {
        if ( is_array($chatIdOrData) ) return $this->api('getChatMemberCount', $chatIdOrData);
        return $this->api('getChatMemberCount', [ 'chat_id' => $chatIdOrData ]);
    }

    public function getChatMember( $chatIdOrData, $userId = null ) {
        if ( is_array($chatIdOrData) ) return $this->api('getChatMember', $chatIdOrData);
        return $this->api('getChatMember', [ 'chat_id' => $chatIdOrData, 'user_id' => $userId ]);
    }

    public function setChatStickerSet( $chatIdOrData, $stickerSetName = null ) {
        if ( is_array($chatIdOrData) ) return $this->api('setChatStickerSet', $chatIdOrData);
        return $this->api('setChatStickerSet', [ 'chat_id' => $chatIdOrData, 'sticker_set_name' => $stickerSetName ]);
    }

    public function deleteChatStickerSet( $chatIdOrData ) {
        if ( is_array($chatIdOrData) ) return $this->api('deleteChatStickerSet', $chatIdOrData);
        return $this->api('deleteChatStickerSet', [ 'chat_id' => $chatIdOrData ]);
    }

    public function getUserChatBoosts( $chatIdOrData, $userId = null ) {
        if ( is_array($chatIdOrData) ) return $this->api('getUserChatBoosts', $chatIdOrData);
        return $this->api('getUserChatBoosts', [ 'chat_id' => $chatIdOrData, 'user_id' => $userId ]);
    }
}
