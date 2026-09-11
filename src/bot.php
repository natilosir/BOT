<?php

namespace natilosir\bot;

require_once __DIR__ . '/http/httpfunc.php';

class bot {
    private static $keyboard = [];

    public static function clearCache() {
        self::$keyboard = [];
    }

    public static function row( $buttons ) {
        self::$keyboard[] = $buttons;

        return new self();
    }

    public static function column( $text, $callback_data = null, $url = null ) {
        $button = [ 'text' => $text ];
        if ( $callback_data ) {
            $button['callback_data'] = $callback_data;
        }
        if ( $url ) {
            $button['url'] = $url;
        }

        return $button;
    }

    public static function sendChatAction( $chatID, $action ) {
        $data = [
            'chat_id' => $chatID,
            'action'  => $action,
        ];

        return http('sendChatAction', $data);
    }

    public static function alert( $query_id, $text, $show_alert = false ) {
        $data = [
            'callback_query_id' => $query_id,
            'text'              => $text,
            'show_alert'        => $show_alert ? 'true' : 'false',
        ];

        return http('answerCallbackQuery', $data);
    }

    public static function sendPhoto( $chatID, $caption = null, $photo = null, $reply_to_message_id = null, $reply_markup = null ) {
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

        return http('sendPhoto', $data);
    }

    public static function editPhotoCaption( $chatID, $message_id, $caption, $reply_markup = null ) {
        $data = [
            'chat_id'    => $chatID,
            'message_id' => $message_id,
            'caption'    => $caption,
            'parse_mode' => 'HTML',
        ];

        if ( $reply_markup ) {
            $data['reply_markup'] = $reply_markup;
        }

        return http('editMessageCaption', $data);
    }

    public static function forwardMessage( $chatID, $from_chat_id, $message_id ) {
        $data = [
            'chat_id'      => $chatID,
            'from_chat_id' => $from_chat_id,
            'message_id'   => $message_id,
        ];

        return http('forwardMessage', $data);
    }

    public static function deleteMessage( $chatID, $message_id ) {
        $data = [
            'chat_id'    => $chatID,
            'message_id' => $message_id,
        ];

        return http('deleteMessage', $data);
    }

    public static function inline( $chatID, $second_OR_text, $message_id, $copy = false ) {
        $reply_markup = json_encode([ 'inline_keyboard' => self::$keyboard ]);

        if ( $copy === 'edit' ) {
            return self::editMessageReplyMarkup($chatID, $message_id, $reply_markup);
        }
        if ( $copy ) {
            return self::copyMessage($chatID, $second_OR_text, $message_id, $reply_markup);
        }
        else {
            return self::sendMessage($chatID, $second_OR_text, $message_id, $reply_markup);
        }
    }

    public static function editMessageReplyMarkup( $chatID, $message_id, $reply_markup = null ) {
        $data = [
            'chat_id'    => $chatID,
            'message_id' => $message_id,
        ];

        if ( $reply_markup ) {
            $data['reply_markup'] = $reply_markup;
        }

        return http('editMessageReplyMarkup', $data);
    }

    public static function copyMessage( $chatID, $second_chat_id, $message_id, $reply_markup = null ) {
        $data = [
            'chat_id'      => $chatID,
            'from_chat_id' => $second_chat_id,
            'message_id'   => $message_id,
        ];

        if ( $reply_markup ) {
            $data['reply_markup'] = $reply_markup;
        }

        return http('copyMessage', $data);
    }

    public static function sendMessage( $chatID, $text, $reply_to_message_id = null, $reply_markup = null ) {
        $data = [
            'chat_id'    => $chatID,
            'text'       => $text,
            'parse_mode' => 'HTML',
        ];

        if ( $reply_markup ) {
            $data['reply_markup'] = $reply_markup;
        }
        if ( $reply_to_message_id ) {
            $data['reply_to_message_id'] = $reply_to_message_id;
        }

        return http('sendMessage', $data);
    }

    public static function keyboard( $chatID, $second_OR_text, $message_id, $copy = false, $resize = true, $one_time = false ) {
        $reply_markup = [
            'keyboard' => self::$keyboard,
        ];

        if ( $resize ) {
            $reply_markup['resize_keyboard'] = $resize;
        }

        if ( $one_time ) {
            $reply_markup['one_time_keyboard'] = $one_time;
        }

        $reply_markup = json_encode($reply_markup);
        if ( $copy ) {
            return self::copyMessage($chatID, $second_OR_text, $message_id, $reply_markup);
        }
        else {
            return self::sendMessage($chatID, $second_OR_text, $message_id, $reply_markup);
        }
    }
}
