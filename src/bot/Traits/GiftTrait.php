<?php

namespace natilosir\bot\bot\Traits;

trait GiftTrait {
    public function getAvailableGifts( array $data = [] ) {
        return $this->api('getAvailableGifts', $data);
    }

    public function sendGift( $userIdOrData = null, $chatId = null, $giftId = null, $payForUpgrade = null, $text = null, $textParseMode = null, $textEntities = null ) {
        if ( is_array($userIdOrData) ) return $this->api('sendGift', $userIdOrData);
        $data = [];
        $this->addOptional($data, 'user_id', $userIdOrData);
        $this->addOptional($data, 'chat_id', $chatId);
        $data['gift_id'] = $giftId;
        $this->addOptional($data, 'pay_for_upgrade', $payForUpgrade);
        $this->addOptional($data, 'text', $text);
        $this->addOptional($data, 'text_parse_mode', $textParseMode);
        $this->addOptional($data, 'text_entities', $textEntities);
        return $this->api('sendGift', $data);
    }

    public function getUserGifts( $userIdOrData, $excludeUnlimited = null, $excludeLimitedUpgradable = null, $excludeLimitedNonUpgradable = null, $excludeUnique = null, $sortByPrice = null, $offset = null, $limit = null ) {
        if ( is_array($userIdOrData) ) return $this->api('getUserGifts', $userIdOrData);
        $data = [ 'user_id' => $userIdOrData ];
        $this->addOptional($data, 'exclude_unlimited', $excludeUnlimited);
        $this->addOptional($data, 'exclude_limited_upgradable', $excludeLimitedUpgradable);
        $this->addOptional($data, 'exclude_limited_non_upgradable', $excludeLimitedNonUpgradable);
        $this->addOptional($data, 'exclude_unique', $excludeUnique);
        $this->addOptional($data, 'sort_by_price', $sortByPrice);
        $this->addOptional($data, 'offset', $offset);
        $this->addOptional($data, 'limit', $limit);
        return $this->api('getUserGifts', $data);
    }

    public function getChatGifts( $chatIdOrData, $excludeUnlimited = null, $excludeLimitedUpgradable = null, $excludeLimitedNonUpgradable = null, $excludeUnique = null, $sortByPrice = null, $offset = null, $limit = null ) {
        if ( is_array($chatIdOrData) ) return $this->api('getChatGifts', $chatIdOrData);
        $data = [ 'chat_id' => $chatIdOrData ];
        $this->addOptional($data, 'exclude_unlimited', $excludeUnlimited);
        $this->addOptional($data, 'exclude_limited_upgradable', $excludeLimitedUpgradable);
        $this->addOptional($data, 'exclude_limited_non_upgradable', $excludeLimitedNonUpgradable);
        $this->addOptional($data, 'exclude_unique', $excludeUnique);
        $this->addOptional($data, 'sort_by_price', $sortByPrice);
        $this->addOptional($data, 'offset', $offset);
        $this->addOptional($data, 'limit', $limit);
        return $this->api('getChatGifts', $data);
    }

    public function giftPremiumSubscription( ...$args ) {
        $data = $this->buildApiData($args);
        return $this->api('giftPremiumSubscription', $data);
    }
}
