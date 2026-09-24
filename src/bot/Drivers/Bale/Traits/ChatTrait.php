<?php

namespace natilosir\bot\Bot\Drivers\Bale\Traits;

use Illuminate\Support\Arr;

trait ChatTrait {
    public function banChatMember( $chatIdOrData, $userId = null ): mixed {
        return $this->chatUserMethod('banChatMember', $chatIdOrData, $userId);
    }

    public function unbanChatMember( $chatIdOrData, $userId = null, $onlyIfBanned = null ): mixed {
        if ( is_array($chatIdOrData) ) return $this->api('unbanChatMember', $chatIdOrData);
        $data = [ 'chat_id' => $chatIdOrData, 'user_id' => $userId ];
        Arr::set($data, 'only_if_banned', $onlyIfBanned);
        return $this->api('unbanChatMember', $data);
    }

    public function promoteChatMember( $chatIdOrData, $userId = null, array $rights = [] ): mixed {
        if ( is_array($chatIdOrData) ) return $this->api('promoteChatMember', $chatIdOrData);
        return $this->api('promoteChatMember', array_merge([
            'chat_id' => $chatIdOrData,
            'user_id' => $userId,
        ], $rights));
    }

    public function setChatPhoto( $chatIdOrData, $photo = null ): mixed {
        if ( is_array($chatIdOrData) ) return $this->api('setChatPhoto', $chatIdOrData);
        return $this->api('setChatPhoto', [ 'chat_id' => $chatIdOrData, 'photo' => $photo ]);
    }

    public function leaveChat( $chatIdOrData ): mixed {
        return $this->chatMethod('leaveChat', $chatIdOrData);
    }

    public function getChat( $chatIdOrData ): mixed {
        return $this->chatMethod('getChat', $chatIdOrData);
    }

    public function getChatAdministrators( $chatIdOrData ): mixed {
        return $this->chatMethod('getChatAdministrators', $chatIdOrData);
    }

    public function getChatMembersCount( $chatIdOrData ): mixed {
        return $this->chatMethod('getChatMembersCount', $chatIdOrData);
    }

    public function getChatMemberCount( $chatIdOrData ): mixed {
        return $this->getChatMembersCount($chatIdOrData);
    }

    public function getChatMember( $chatIdOrData, $userId = null ): mixed {
        return $this->chatUserMethod('getChatMember', $chatIdOrData, $userId);
    }

    public function pinChatMessage( $chatIdOrData, $messageId = null ): mixed {
        return $this->chatMessageMethod('pinChatMessage', $chatIdOrData, $messageId);
    }

    public function unpinChatMessage( $chatIdOrData, $messageId = null ): mixed {
        return $this->chatMessageMethod('unPinChatMessage', $chatIdOrData, $messageId);
    }

    public function unpinAllChatMessages( $chatIdOrData ): mixed {
        return $this->chatMethod('unpinAllChatMessages', $chatIdOrData);
    }

    public function setChatTitle( $chatIdOrData, $title = null ): mixed {
        if ( is_array($chatIdOrData) ) return $this->api('setChatTitle', $chatIdOrData);
        return $this->api('setChatTitle', [ 'chat_id' => $chatIdOrData, 'title' => $title ]);
    }

    public function setChatDescription( $chatIdOrData, $description = null ): mixed {
        if ( is_array($chatIdOrData) ) return $this->api('setChatDescription', $chatIdOrData);
        return $this->api('setChatDescription', [ 'chat_id' => $chatIdOrData, 'description' => $description ]);
    }

    public function deleteChatPhoto( $chatIdOrData ): mixed {
        return $this->chatMethod('deleteChatPhoto', $chatIdOrData);
    }

    public function createChatInviteLink( $chatIdOrData ): mixed {
        return $this->chatMethod('createChatInviteLink', $chatIdOrData);
    }

    public function revokeChatInviteLink( $chatIdOrData, $inviteLink = null ): mixed {
        if ( is_array($chatIdOrData) ) return $this->api('revokeChatInviteLink', $chatIdOrData);
        return $this->api('revokeChatInviteLink', [ 'chat_id' => $chatIdOrData, 'invite_link' => $inviteLink ]);
    }

    public function exportChatInviteLink( $chatIdOrData ): mixed {
        return $this->chatMethod('exportChatInviteLink', $chatIdOrData);
    }

    protected function chatMethod( string $method, $chatIdOrData ): mixed {
        if ( is_array($chatIdOrData) ) return $this->api($method, $chatIdOrData);
        return $this->api($method, [ 'chat_id' => $chatIdOrData ]);
    }

    protected function chatUserMethod( string $method, $chatIdOrData, $userId ): mixed {
        if ( is_array($chatIdOrData) ) return $this->api($method, $chatIdOrData);
        return $this->api($method, [ 'chat_id' => $chatIdOrData, 'user_id' => $userId ]);
    }

    protected function chatMessageMethod( string $method, $chatIdOrData, $messageId ): mixed {
        if ( is_array($chatIdOrData) ) return $this->api($method, $chatIdOrData);
        return $this->api($method, [ 'chat_id' => $chatIdOrData, 'message_id' => $messageId ]);
    }
}
