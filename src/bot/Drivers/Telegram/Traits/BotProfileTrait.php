<?php

namespace natilosir\bot\Bot\Drivers\Telegram\Traits;

trait BotProfileTrait {
    public function setMyCommands( $commandsOrData, $scope = null, $languageCode = null ) {
        if ( is_array($commandsOrData) && array_key_exists('commands', $commandsOrData) ) {
            return $this->api('setMyCommands', $commandsOrData);
        }

        $data = [ 'commands' => $commandsOrData ];
        $this->addOptional($data, 'scope', $scope);
        $this->addOptional($data, 'language_code', $languageCode);
        return $this->api('setMyCommands', $data);
    }

    public function deleteMyCommands( $scopeOrData = null, $languageCode = null ) {
        if ( is_array($scopeOrData) && ( array_key_exists('scope', $scopeOrData) || array_key_exists('language_code', $scopeOrData) ) ) {
            return $this->api('deleteMyCommands', $scopeOrData);
        }
        $data = [];
        $this->addOptional($data, 'scope', $scopeOrData);
        $this->addOptional($data, 'language_code', $languageCode);
        return $this->api('deleteMyCommands', $data);
    }

    public function getMyCommands( $scopeOrData = null, $languageCode = null ) {
        if ( is_array($scopeOrData) && ( array_key_exists('scope', $scopeOrData) || array_key_exists('language_code', $scopeOrData) ) ) {
            return $this->api('getMyCommands', $scopeOrData);
        }
        $data = [];
        $this->addOptional($data, 'scope', $scopeOrData);
        $this->addOptional($data, 'language_code', $languageCode);
        return $this->api('getMyCommands', $data);
    }

    public function setMyName( $nameOrData = null, $languageCode = null ) {
        if ( is_array($nameOrData) ) return $this->api('setMyName', $nameOrData);
        $data = [];
        $this->addOptional($data, 'name', $nameOrData);
        $this->addOptional($data, 'language_code', $languageCode);
        return $this->api('setMyName', $data);
    }

    public function getMyName( $languageCodeOrData = null ) {
        if ( is_array($languageCodeOrData) ) return $this->api('getMyName', $languageCodeOrData);
        $data = [];
        $this->addOptional($data, 'language_code', $languageCodeOrData);
        return $this->api('getMyName', $data);
    }

    public function setMyDescription( $descriptionOrData = null, $languageCode = null ) {
        if ( is_array($descriptionOrData) ) return $this->api('setMyDescription', $descriptionOrData);
        $data = [];
        $this->addOptional($data, 'description', $descriptionOrData);
        $this->addOptional($data, 'language_code', $languageCode);
        return $this->api('setMyDescription', $data);
    }

    public function getMyDescription( $languageCodeOrData = null ) {
        if ( is_array($languageCodeOrData) ) return $this->api('getMyDescription', $languageCodeOrData);
        $data = [];
        $this->addOptional($data, 'language_code', $languageCodeOrData);
        return $this->api('getMyDescription', $data);
    }

    public function setMyShortDescription( $shortDescriptionOrData = null, $languageCode = null ) {
        if ( is_array($shortDescriptionOrData) ) return $this->api('setMyShortDescription', $shortDescriptionOrData);
        $data = [];
        $this->addOptional($data, 'short_description', $shortDescriptionOrData);
        $this->addOptional($data, 'language_code', $languageCode);
        return $this->api('setMyShortDescription', $data);
    }

    public function getMyShortDescription( $languageCodeOrData = null ) {
        if ( is_array($languageCodeOrData) ) return $this->api('getMyShortDescription', $languageCodeOrData);
        $data = [];
        $this->addOptional($data, 'language_code', $languageCodeOrData);
        return $this->api('getMyShortDescription', $data);
    }

    public function setMyProfilePhoto( ...$args ) {
        $data = $this->buildApiData($args);
        return $this->api('setMyProfilePhoto', $data);
    }

    public function removeMyProfilePhoto( array $data = [] ) {
        return $this->api('removeMyProfilePhoto', $data);
    }

    public function setChatMenuButton( array $data = [] ) {
        return $this->api('setChatMenuButton', $data);
    }

    public function getChatMenuButton( array $data = [] ) {
        return $this->api('getChatMenuButton', $data);
    }

    public function setMyDefaultAdministratorRights( array $data = [] ) {
        return $this->api('setMyDefaultAdministratorRights', $data);
    }

    public function getMyDefaultAdministratorRights( array $data = [] ) {
        return $this->api('getMyDefaultAdministratorRights', $data);
    }

    public function setUserEmojiStatus( $userIdOrData, $emojiStatusCustomEmojiId = null, $emojiStatusExpirationDate = null ) {
        if ( is_array($userIdOrData) && func_num_args() === 1 ) {
            return $this->api('setUserEmojiStatus', $userIdOrData);
        }

        $data = [ 'user_id' => $userIdOrData ];
        $this->addOptional($data, 'emoji_status_custom_emoji_id', $emojiStatusCustomEmojiId);
        $this->addOptional($data, 'emoji_status_expiration_date', $emojiStatusExpirationDate);

        return $this->api('setUserEmojiStatus', $data);
    }
}
