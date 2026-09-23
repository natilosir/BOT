<?php

namespace natilosir\bot\Bot\Drivers\Bale\Traits;

trait MediaTrait {
    public function sendPhoto( $chatIdOrData, $caption = null, $photo = null, $replyToMessageId = null, $replyMarkup = null ): mixed {
        return $this->sendMedia('sendPhoto', 'photo', $chatIdOrData, $photo, $caption, $replyToMessageId, $replyMarkup);
    }

    public function sendAudio( $chatIdOrData, $audio = null, $caption = null, $replyToMessageId = null, $replyMarkup = null ): mixed {
        return $this->sendMedia('sendAudio', 'audio', $chatIdOrData, $audio, $caption, $replyToMessageId, $replyMarkup);
    }

    public function sendDocument( $chatIdOrData, $document = null, $caption = null, $replyToMessageId = null, $replyMarkup = null ): mixed {
        return $this->sendMedia('sendDocument', 'document', $chatIdOrData, $document, $caption, $replyToMessageId, $replyMarkup);
    }

    public function sendVideo( $chatIdOrData, $video = null, $caption = null, $replyToMessageId = null, $replyMarkup = null ): mixed {
        return $this->sendMedia('sendVideo', 'video', $chatIdOrData, $video, $caption, $replyToMessageId, $replyMarkup);
    }

    public function sendAnimation( $chatIdOrData, $animation = null, $caption = null, $replyToMessageId = null, $replyMarkup = null ): mixed {
        return $this->sendMedia('sendAnimation', 'animation', $chatIdOrData, $animation, $caption, $replyToMessageId, $replyMarkup);
    }

    public function sendVoice( $chatIdOrData, $voice = null, $caption = null, $replyToMessageId = null, $replyMarkup = null ): mixed {
        return $this->sendMedia('sendVoice', 'voice', $chatIdOrData, $voice, $caption, $replyToMessageId, $replyMarkup);
    }

    public function sendMediaGroup( $chatIdOrData, $media = null, $replyToMessageId = null ): mixed {
        if ( is_array($chatIdOrData) ) return $this->api('sendMediaGroup', $chatIdOrData);
        $data = [ 'chat_id' => $chatIdOrData, 'media' => $media ];
        $this->addOptional($data, 'reply_to_message_id', $replyToMessageId);
        return $this->api('sendMediaGroup', $data);
    }

    public function sendLocation( $chatIdOrData, $latitude = null, $longitude = null, $horizontalAccuracy = null, $replyToMessageId = null, $replyMarkup = null ): mixed {
        if ( is_array($chatIdOrData) ) return $this->api('sendLocation', $chatIdOrData);
        $data = [ 'chat_id' => $chatIdOrData, 'latitude' => $latitude, 'longitude' => $longitude ];
        $this->addOptional($data, 'horizontal_accuracy', $horizontalAccuracy);
        $this->addOptional($data, 'reply_to_message_id', $replyToMessageId);
        $this->addOptional($data, 'reply_markup', $replyMarkup);
        return $this->api('sendLocation', $data);
    }

    public function sendContact( $chatIdOrData, $phoneNumber = null, $firstName = null, $lastName = null, $replyToMessageId = null, $replyMarkup = null ): mixed {
        if ( is_array($chatIdOrData) ) return $this->api('sendContact', $chatIdOrData);
        $data = [ 'chat_id' => $chatIdOrData, 'phone_number' => $phoneNumber, 'first_name' => $firstName ];
        $this->addOptional($data, 'last_name', $lastName);
        $this->addOptional($data, 'reply_to_message_id', $replyToMessageId);
        $this->addOptional($data, 'reply_markup', $replyMarkup);
        return $this->api('sendContact', $data);
    }

    protected function sendMedia( string $method, string $field, $chatIdOrData, $media, $caption, $replyToMessageId, $replyMarkup ): mixed {
        if ( is_array($chatIdOrData) ) return $this->api($method, $chatIdOrData);
        $data = [ 'chat_id' => $chatIdOrData, $field => $media ];
        $this->addOptional($data, 'caption', $caption);
        $this->addOptional($data, 'reply_to_message_id', $replyToMessageId);
        $this->addOptional($data, 'reply_markup', $replyMarkup);
        return $this->api($method, $data);
    }
}
