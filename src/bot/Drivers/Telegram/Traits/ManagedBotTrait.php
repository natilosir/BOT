<?php

namespace natilosir\bot\Bot\Drivers\Telegram\Traits;

trait ManagedBotTrait {
    public function getManagedBotToken( $userIdOrData ) {
        if ( is_array($userIdOrData) ) return $this->api('getManagedBotToken', $userIdOrData);
        return $this->api('getManagedBotToken', [ 'user_id' => $userIdOrData ]);
    }

    public function replaceManagedBotToken( $userIdOrData ) {
        if ( is_array($userIdOrData) ) return $this->api('replaceManagedBotToken', $userIdOrData);
        return $this->api('replaceManagedBotToken', [ 'user_id' => $userIdOrData ]);
    }

    public function getManagedBotAccessSettings( $userIdOrData ) {
        if ( is_array($userIdOrData) ) return $this->api('getManagedBotAccessSettings', $userIdOrData);
        return $this->api('getManagedBotAccessSettings', [ 'user_id' => $userIdOrData ]);
    }

    public function setManagedBotAccessSettings( $userIdOrData, $isAccessRestricted = null, $addedUserIds = null ) {
        if ( is_array($userIdOrData) ) return $this->api('setManagedBotAccessSettings', $userIdOrData);
        $data = [ 'user_id' => $userIdOrData, 'is_access_restricted' => $isAccessRestricted ];
        $this->addOptional($data, 'added_user_ids', $addedUserIds);
        return $this->api('setManagedBotAccessSettings', $data);
    }

    public function getUserPersonalChatMessages( ...$args ) {
        $data = $this->buildApiData($args);
        return $this->api('getUserPersonalChatMessages', $data);
    }
}
