<?php

namespace natilosir\bot\bot\Traits;

trait MediaTrait {
    // Backward-compatible legacy method.
    public function sendPhoto( $chatID, $caption = null, $photo = null, $reply_to_message_id = null, $reply_markup = null ) {
        $data = [
            'chat_id' => $chatID,
        ];

        if ( $caption ) {
            $data['caption']    = mb_convert_encoding($caption, 'UTF-8', 'UTF-8');
            $data['parse_mode'] = 'HTML';
        }

        if ( $reply_to_message_id ) {
            $data['reply_to_message_id'] = $reply_to_message_id;
        }

        if ( $reply_markup ) {
            $data['reply_markup'] = $reply_markup;
        }

        if ( $photo ) {
            if ( is_array($photo) && isset($photo['tmp_name']) ) {
                $data['photo'] = $photo;
            }
            elseif ( is_string($photo) && file_exists($photo) ) {
                $data['photo'] = [
                    'tmp_name' => $photo,
                    'name'     => basename($photo),
                ];
            }
            elseif ( is_string($photo) && preg_match('/^https?:\/\//', $photo) ) {
                $data['photo'] = $photo;
            }
            else {
                $tempFile = tempnam(sys_get_temp_dir(), 'tg_img_');
                file_put_contents($tempFile, $photo);

                $data['photo'] = [
                    'tmp_name' => $tempFile,
                    'name'     => 'image.jpg',
                ];

                register_shutdown_function(function () use ( $tempFile ) {
                    if ( file_exists($tempFile) ) {
                        @unlink($tempFile);
                    }
                });
            }
        }

        return $this->client()
            ->api('sendPhoto', $data);
    }

    // Backward-compatible legacy method.
    public function editPhotoCaption( $chatID, $message_id, $caption, $reply_markup = null ) {
        $data = [
            'chat_id'    => $chatID,
            'message_id' => $message_id,
            'caption'    => $caption,
            'parse_mode' => 'HTML',
        ];

        if ( $reply_markup ) {
            $data['reply_markup'] = $reply_markup;
        }

        return $this->client()
            ->api('editMessageCaption', $data);
    }

    public function sendPhotoRaw( ...$args ) {
        $data = $this->buildApiData($args);
        return $this->api('sendPhoto', $data);
    }

    public function sendAudio( $chatIdOrData, $audio = null, $caption = null, $parseMode = 'HTML', $replyMarkup = null, array $extra = [] ) {
        return $this->sendMediaPayload('sendAudio', 'audio', $chatIdOrData, $audio, $caption, $parseMode, $replyMarkup, $extra);
    }

    public function sendDocument( $chatIdOrData, $document = null, $caption = null, $parseMode = 'HTML', $replyMarkup = null, array $extra = [] ) {
        return $this->sendMediaPayload('sendDocument', 'document', $chatIdOrData, $document, $caption, $parseMode, $replyMarkup, $extra);
    }

    public function sendVideo( $chatIdOrData, $video = null, $caption = null, $parseMode = 'HTML', $replyMarkup = null, array $extra = [] ) {
        return $this->sendMediaPayload('sendVideo', 'video', $chatIdOrData, $video, $caption, $parseMode, $replyMarkup, $extra);
    }

    public function sendAnimation( $chatIdOrData, $animation = null, $caption = null, $parseMode = 'HTML', $replyMarkup = null, array $extra = [] ) {
        return $this->sendMediaPayload('sendAnimation', 'animation', $chatIdOrData, $animation, $caption, $parseMode, $replyMarkup, $extra);
    }

    public function sendVoice( $chatIdOrData, $voice = null, $caption = null, $parseMode = 'HTML', $replyMarkup = null, array $extra = [] ) {
        return $this->sendMediaPayload('sendVoice', 'voice', $chatIdOrData, $voice, $caption, $parseMode, $replyMarkup, $extra);
    }

    public function sendVideoNote( $chatIdOrData, $videoNote = null, $replyMarkup = null, array $extra = [] ) {
        if ( is_array($chatIdOrData) ) return $this->api('sendVideoNote', $chatIdOrData);
        $data = [ 'chat_id' => $chatIdOrData, 'video_note' => $videoNote ];
        $this->addOptional($data, 'reply_markup', $replyMarkup);
        return $this->api('sendVideoNote', $this->withExtra($data, $extra));
    }

    public function sendLivePhoto( ...$args ) {
        $data = $this->buildApiData($args);
        return $this->api('sendLivePhoto', $data);
    }

    public function sendPaidMedia( ...$args ) {
        $data = $this->buildApiData($args);
        return $this->api('sendPaidMedia', $data);
    }

    public function sendMediaGroup( $chatIdOrData, $media = null, array $extra = [] ) {
        if ( is_array($chatIdOrData) ) return $this->api('sendMediaGroup', $chatIdOrData);
        return $this->api('sendMediaGroup', $this->withExtra([
            'chat_id' => $chatIdOrData,
            'media'   => $media,
        ], $extra));
    }

    public function sendSticker( $chatIdOrData, $sticker = null, $emoji = null, array $extra = [] ) {
        if ( is_array($chatIdOrData) ) return $this->api('sendSticker', $chatIdOrData);
        $data = [ 'chat_id' => $chatIdOrData, 'sticker' => $sticker ];
        $this->addOptional($data, 'emoji', $emoji);
        return $this->api('sendSticker', $this->withExtra($data, $extra));
    }

    protected function sendMediaPayload( $method, $mediaKey, $chatIdOrData, $media, $caption, $parseMode, $replyMarkup, array $extra ) {
        if ( is_array($chatIdOrData) ) return $this->api($method, $chatIdOrData);

        $data = [ 'chat_id' => $chatIdOrData, $mediaKey => $media ];
        $this->addOptional($data, 'caption', $caption);
        if ( $caption !== null ) $this->addOptional($data, 'parse_mode', $parseMode);
        $this->addOptional($data, 'reply_markup', $replyMarkup);

        return $this->api($method, $this->withExtra($data, $extra));
    }
}
