<?php

namespace natilosir\bot\Bot\Drivers\Telegram\Traits;

use Illuminate\Support\Arr;

trait GiftTrait {
    public function getAvailableGifts( array $data = [] ) {
        return $this->api('getAvailableGifts', $data);
    }

    public function sendGift( $userIdOrData = null, $chatId = null, $giftId = null, $payForUpgrade = null, $text = null, $textParseMode = null, $textEntities = null ) {
        if ( is_array($userIdOrData) ) return $this->api('sendGift', $userIdOrData);
        $data = [];
        Arr::set($data, 'user_id', $userIdOrData);
        Arr::set($data, 'chat_id', $chatId);
        $data['gift_id'] = $giftId;
        Arr::set($data, 'pay_for_upgrade', $payForUpgrade);
        Arr::set($data, 'text', $text);
        Arr::set($data, 'text_parse_mode', $textParseMode);
        Arr::set($data, 'text_entities', $textEntities);
        return $this->api('sendGift', $data);
    }

    public function getUserGifts( $userIdOrData, $excludeUnlimited = null, $excludeLimitedUpgradable = null, $excludeLimitedNonUpgradable = null, $excludeUnique = null, $sortByPrice = null, $offset = null, $limit = null ) {
        if ( is_array($userIdOrData) ) return $this->api('getUserGifts', $userIdOrData);
        $data = [ 'user_id' => $userIdOrData ];
        Arr::set($data, 'exclude_unlimited', $excludeUnlimited);
        Arr::set($data, 'exclude_limited_upgradable', $excludeLimitedUpgradable);
        Arr::set($data, 'exclude_limited_non_upgradable', $excludeLimitedNonUpgradable);
        Arr::set($data, 'exclude_unique', $excludeUnique);
        Arr::set($data, 'sort_by_price', $sortByPrice);
        Arr::set($data, 'offset', $offset);
        Arr::set($data, 'limit', $limit);
        return $this->api('getUserGifts', $data);
    }

    public function getChatGifts( $chatIdOrData, $excludeUnlimited = null, $excludeLimitedUpgradable = null, $excludeLimitedNonUpgradable = null, $excludeUnique = null, $sortByPrice = null, $offset = null, $limit = null ) {
        if ( is_array($chatIdOrData) ) return $this->api('getChatGifts', $chatIdOrData);
        $data = [ 'chat_id' => $chatIdOrData ];
        Arr::set($data, 'exclude_unlimited', $excludeUnlimited);
        Arr::set($data, 'exclude_limited_upgradable', $excludeLimitedUpgradable);
        Arr::set($data, 'exclude_limited_non_upgradable', $excludeLimitedNonUpgradable);
        Arr::set($data, 'exclude_unique', $excludeUnique);
        Arr::set($data, 'sort_by_price', $sortByPrice);
        Arr::set($data, 'offset', $offset);
        Arr::set($data, 'limit', $limit);
        return $this->api('getChatGifts', $data);
    }

    public function giftPremiumSubscription( ...$args ) {
        return $this->apiFromArguments('giftPremiumSubscription', $args);
    }
}
