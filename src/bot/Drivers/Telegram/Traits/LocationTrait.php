<?php

namespace natilosir\bot\Bot\Drivers\Telegram\Traits;

trait LocationTrait {
    public function sendLocation( $chatIdOrData, $latitude = null, $longitude = null, array $options = [] ) {
        if ( is_array($chatIdOrData) ) return $this->api('sendLocation', $chatIdOrData);
        return $this->api('sendLocation', $this->withExtra([
            'chat_id'   => $chatIdOrData,
            'latitude'  => $latitude,
            'longitude' => $longitude,
        ], $options));
    }

    public function editMessageLiveLocation( ...$args ) {
        $data = $this->buildApiData($args);
        return $this->api('editMessageLiveLocation', $data);
    }

    public function stopMessageLiveLocation( ...$args ) {
        $data = $this->buildApiData($args);
        return $this->api('stopMessageLiveLocation', $data);
    }

    public function sendVenue( $chatIdOrData, $latitude = null, $longitude = null, $title = null, $address = null, array $options = [] ) {
        if ( is_array($chatIdOrData) ) return $this->api('sendVenue', $chatIdOrData);
        return $this->api('sendVenue', $this->withExtra([
            'chat_id'   => $chatIdOrData,
            'latitude'  => $latitude,
            'longitude' => $longitude,
            'title'     => $title,
            'address'   => $address,
        ], $options));
    }

    public function sendContact( $chatIdOrData, $phoneNumber = null, $firstName = null, $lastName = null, $vcard = null, array $options = [] ) {
        if ( is_array($chatIdOrData) ) return $this->api('sendContact', $chatIdOrData);
        $data = [ 'chat_id' => $chatIdOrData, 'phone_number' => $phoneNumber, 'first_name' => $firstName ];
        $this->addOptional($data, 'last_name', $lastName);
        $this->addOptional($data, 'vcard', $vcard);
        return $this->api('sendContact', $this->withExtra($data, $options));
    }
}
