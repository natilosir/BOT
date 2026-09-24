<?php

namespace natilosir\bot\Bot\Drivers\Telegram\Traits;

use Illuminate\Support\Arr;

trait VerificationTrait {
    public function verifyUser( $userIdOrData, $customDescription = null ) {
        if ( is_array($userIdOrData) ) return $this->api('verifyUser', $userIdOrData);
        $data = [ 'user_id' => $userIdOrData ];
        Arr::set($data, 'custom_description', $customDescription);
        return $this->api('verifyUser', $data);
    }

    public function verifyChat( $chatIdOrData, $customDescription = null ) {
        if ( is_array($chatIdOrData) ) return $this->api('verifyChat', $chatIdOrData);
        $data = [ 'chat_id' => $chatIdOrData ];
        Arr::set($data, 'custom_description', $customDescription);
        return $this->api('verifyChat', $data);
    }

    public function removeUserVerification( $userIdOrData ) {
        if ( is_array($userIdOrData) ) return $this->api('removeUserVerification', $userIdOrData);
        return $this->api('removeUserVerification', [ 'user_id' => $userIdOrData ]);
    }

    public function removeChatVerification( $chatIdOrData ) {
        if ( is_array($chatIdOrData) ) return $this->api('removeChatVerification', $chatIdOrData);
        return $this->api('removeChatVerification', [ 'chat_id' => $chatIdOrData ]);
    }
}
