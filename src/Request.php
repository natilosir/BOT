<?php

namespace natilosir\bot;

class Request {
    private $data;
    private $updateTypes = [
        'message',
        'edited_message',
        'channel_post',
        'edited_channel_post',
        'inline_query',
        'chosen_inline_result',
        'callback_query',
        'shipping_query',
        'pre_checkout_query',
        'poll',
        'poll_answer',
        'my_chat_member',
        'chat_member',
        'chat_join_request',
    ];

    // Common fields
    public $updateId;
    public $updateType;
    public $chatID;
    public $fromID;
    public $firstName;
    public $lastName;
    public $username;
    public $date;
    public $text;
    public $message_id;

    // Message specific
    public $entities;
    public $caption;
    public $photo;
    public $audio;
    public $document;
    public $video;
    public $voice;
    public $contact;
    public $location;
    public $venue;
    public $sticker;
    public $animation;
    public $dice;
    public $new_chat_members;
    public $left_chat_member;
    public $new_chat_title;
    public $new_chat_photo;
    public $pinned_message;
    public $reply_to_message;

    // Callback query specific
    public $query_id;
    public $callbackData;

    // Inline query specific
    public $inline_query_id;
    public $query;
    public $offset;

    // Shipping query specific
    public $shipping_query_id;
    public $invoice_payload;
    public $shipping_address;

    // Pre-checkout query specific
    public $pre_checkout_query_id;
    public $currency;
    public $total_amount;
    public $order_info;

    // Poll specific
    public $poll_id;
    public $question;
    public $options;
    public $total_voter_count;
    public $is_closed;
    public $is_anonymous;

    // Chat member updates
    public $old_chat_member;
    public $new_chat_member;
    public $invite_link;

    public function __construct() {
        $this->data = json_decode(file_get_contents('php://input'), true);
        $this->parseRequest();
    }

    private function parseRequest() {
        $this->updateId = $this->data['update_id'] ?? null;

        foreach ( $this->updateTypes as $type ) {
            if ( isset($this->data[$type]) ) {
                $this->updateType = $type;
                $method           = 'parse' . str_replace('_', '', ucwords($type, '_'));
                if ( method_exists($this, $method) ) {
                    $this->$method($this->data[$type]);
                }
                break;
            }
        }
    }

    private function parseMessage( array $message ) {
        $this->text       = $message['text'] ?? null;
        $this->chatID     = $message['chat']['id'] ?? null;
        $this->fromID     = $message['from']['id'] ?? null;
        $this->firstName  = $message['from']['first_name'] ?? null;
        $this->lastName   = $message['from']['last_name'] ?? null;
        $this->username   = $message['from']['username'] ?? null;
        $this->date       = $message['date'] ?? null;
        $this->message_id = $message['message_id'] ?? null;

        // Message content
        $this->entities         = $message['entities'] ?? null;
        $this->caption          = $message['caption'] ?? null;
        $this->photo            = $message['photo'] ?? null;
        $this->audio            = $message['audio'] ?? null;
        $this->document         = $message['document'] ?? null;
        $this->video            = $message['video'] ?? null;
        $this->voice            = $message['voice'] ?? null;
        $this->contact          = $message['contact'] ?? null;
        $this->location         = $message['location'] ?? null;
        $this->venue            = $message['venue'] ?? null;
        $this->sticker          = $message['sticker'] ?? null;
        $this->animation        = $message['animation'] ?? null;
        $this->dice             = $message['dice'] ?? null;
        $this->new_chat_members = $message['new_chat_members'] ?? null;
        $this->left_chat_member = $message['left_chat_member'] ?? null;
        $this->new_chat_title   = $message['new_chat_title'] ?? null;
        $this->new_chat_photo   = $message['new_chat_photo'] ?? null;
        $this->pinned_message   = $message['pinned_message'] ?? null;
        $this->reply_to_message = $message['reply_to_message'] ?? null;
    }

    private function parseEditedmessage( array $message ) {
        $this->parseMessage($message);
    }

    private function parseChannelpost( array $message ) {
        $this->parseMessage($message);
    }

    private function parseEditedchannelpost( array $message ) {
        $this->parseMessage($message);
    }

    private function parseCallbackquery( array $callbackQuery ) {
        $this->query_id     = $callbackQuery['id'] ?? null;
        $this->callbackData = $callbackQuery['data'] ?? null;
        $this->chatID       = $callbackQuery['message']['chat']['id'] ?? null;
        $this->message_id   = $callbackQuery['message']['message_id'] ?? null;
        $this->fromID       = $callbackQuery['from']['id'] ?? null;
        $this->firstName    = $callbackQuery['from']['first_name'] ?? null;
        $this->lastName     = $callbackQuery['from']['last_name'] ?? null;
        $this->username     = $callbackQuery['from']['username'] ?? null;
        $this->text         = $this->callbackData;
    }

    private function parseInlinequery( array $inlineQuery ) {
        $this->inline_query_id = $inlineQuery['id'] ?? null;
        $this->query           = $inlineQuery['query'] ?? null;
        $this->offset          = $inlineQuery['offset'] ?? null;
        $this->fromID          = $inlineQuery['from']['id'] ?? null;
        $this->firstName       = $inlineQuery['from']['first_name'] ?? null;
        $this->lastName        = $inlineQuery['from']['last_name'] ?? null;
        $this->username        = $inlineQuery['from']['username'] ?? null;
    }

    private function parseChoseninlineresult( array $chosenResult ) {
        $this->fromID    = $chosenResult['from']['id'] ?? null;
        $this->firstName = $chosenResult['from']['first_name'] ?? null;
        $this->lastName  = $chosenResult['from']['last_name'] ?? null;
        $this->username  = $chosenResult['from']['username'] ?? null;
        $this->query     = $chosenResult['query'] ?? null;
        $this->result_id = $chosenResult['result_id'] ?? null;
    }

    private function parseShippingquery( array $shippingQuery ) {
        $this->shipping_query_id = $shippingQuery['id'] ?? null;
        $this->fromID            = $shippingQuery['from']['id'] ?? null;
        $this->invoice_payload   = $shippingQuery['invoice_payload'] ?? null;
        $this->shipping_address  = $shippingQuery['shipping_address'] ?? null;
    }

    private function parsePrecheckoutquery( array $preCheckoutQuery ) {
        $this->pre_checkout_query_id = $preCheckoutQuery['id'] ?? null;
        $this->fromID                = $preCheckoutQuery['from']['id'] ?? null;
        $this->currency              = $preCheckoutQuery['currency'] ?? null;
        $this->total_amount          = $preCheckoutQuery['total_amount'] ?? null;
        $this->invoice_payload       = $preCheckoutQuery['invoice_payload'] ?? null;
        $this->order_info            = $preCheckoutQuery['order_info'] ?? null;
    }

    private function parsePoll( array $poll ) {
        $this->poll_id           = $poll['id'] ?? null;
        $this->question          = $poll['question'] ?? null;
        $this->options           = $poll['options'] ?? null;
        $this->total_voter_count = $poll['total_voter_count'] ?? null;
        $this->is_closed         = $poll['is_closed'] ?? null;
        $this->is_anonymous      = $poll['is_anonymous'] ?? null;
    }

    private function parsePollanswer( array $pollAnswer ) {
        $this->poll_id    = $pollAnswer['poll_id'] ?? null;
        $this->fromID     = $pollAnswer['user']['id'] ?? null;
        $this->option_ids = $pollAnswer['option_ids'] ?? null;
    }

    private function parseMychatmember( array $chatMemberUpdate ) {
        $this->chatID          = $chatMemberUpdate['chat']['id'] ?? null;
        $this->fromID          = $chatMemberUpdate['from']['id'] ?? null;
        $this->date            = $chatMemberUpdate['date'] ?? null;
        $this->old_chat_member = $chatMemberUpdate['old_chat_member'] ?? null;
        $this->new_chat_member = $chatMemberUpdate['new_chat_member'] ?? null;
    }

    private function parseChatmember( array $chatMemberUpdate ) {
        $this->parseMychatmember($chatMemberUpdate);
    }

    private function parseChatjoinrequest( array $chatJoinRequest ) {
        $this->chatID      = $chatJoinRequest['chat']['id'] ?? null;
        $this->fromID      = $chatJoinRequest['from']['id'] ?? null;
        $this->date        = $chatJoinRequest['date'] ?? null;
        $this->bio         = $chatJoinRequest['bio'] ?? null;
        $this->invite_link = $chatJoinRequest['invite_link'] ?? null;
    }

    public function getInput(): string {
        if ( $this->updateType === 'callback_query' ) {
            return $this->callbackData ?? '';
        }
        elseif ( $this->updateType === 'inline_query' ) {
            return $this->query ?? '';
        }
        return $this->text ?? '';
    }

    public function getUpdateType(): string {
        return $this->updateType ?? '';
    }

    public function getRawData(): array {
        return $this->data;
    }

    public function isCommand(): bool {
        if ( empty($this->entities) ) {
            return false;
        }

        foreach ( $this->entities as $entity ) {
            if ( $entity['type'] === 'bot_command' && $entity['offset'] === 0 ) {
                return true;
            }
        }

        return false;
    }

    public function toArray(): array {
        $result = [];

        $publicProperties = get_object_vars($this);

        foreach ( $publicProperties as $property => $value ) {
            if ( $property === 'data' || $property === 'updateTypes' ) {
                continue;
            }

            if ( $this->isValueNotEmpty($value) ) {
                $result[$property] = $value;
            }
        }

        return $result;
    }

    private function isValueNotEmpty( $value ): bool {
        if ( $value === null || $value === '' ) {
            return false;
        }

        if ( is_array($value) && empty($value) ) {
            return false;
        }

        return true;
    }

    public function toJson(): string {
        return json_encode($this->toArray());
    }

    public function dd(): string {
        return dd($this->toArray());
    }

    public function lg(): string {
        return lg($this->toArray());
    }

    public function getCommand(): string {
        if ( !$this->isCommand() ) {
            return '';
        }

        foreach ( $this->entities as $entity ) {
            if ( $entity['type'] === 'bot_command' && $entity['offset'] === 0 ) {
                return substr($this->text, $entity['offset'], $entity['length']);
            }
        }

        return '';
    }
}