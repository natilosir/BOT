<?php

namespace natilosir\bot;

class Request {

    public $updateId;
    public $request;
    public $updateType;

    // COMMON FIELDS
    public $chatID;
    public $fromID;
    public $firstName;
    public $lastName;
    public $username;
    public $date;
    public $text;
    public $message_id;
    public $entities;
    public $caption;
    public $chat_id;

    // MESSAGE FIELDS
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

    // QUERY FIELDS
    public $query_id;
    public $callbackData;
    public $inline_query_id;
    public $query;
    public $offset;

    // PAYMENT FIELDS
    public $shipping_query_id;
    public $invoice_payload;
    public $shipping_address;
    public $pre_checkout_query_id;
    public $currency;
    public $total_amount;
    public $order_info;

    // POLL FIELDS
    public $poll_id;
    public $question;
    public $options;
    public $total_voter_count;
    public $is_closed;
    public $is_anonymous;
    public $option_ids;

    // CHAT MEMBER
    public $old_chat_member;
    public $new_chat_member;
    public $bio;
    public $invite_link;

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

    public function __construct() {
        $this->parseInput();
        $this->parseRequest();
    }

    private function parseInput() {
        if ( !empty($_POST) || !empty($_FILES) ) {
            $data = $_POST;

            if ( !empty($_FILES) ) {
                $data['_files'] = $_FILES;
            }

            $this->data = $this->normalizeNestedArray($data);
            return;
        }

        $raw  = file_get_contents("php://input");
        $json = json_decode($raw, true);

        if ( is_array($json) ) {
            $this->data = $json;
            return;
        }

        $this->data = [];
    }

    private function normalizeNestedArray( $input ) {
        $result = [];

        foreach ( $input as $key => $value ) {
            if ( preg_match('/^([^\[]+)\[([^\]]*)\]$/', $key, $m) ) {
                $parent = $m[1];
                $child  = $m[2];

                if ( !isset($result[$parent]) ) $result[$parent] = [];

                if ( $child === '' ) {
                    $result[$parent][] = $value;
                }
                else {
                    $result[$parent][$child] = $value;
                }
            }

            else {
                $result[$key] = $value;
            }
        }

        return $result;
    }

    private function parseRequest() {
        if ( !empty($this->data['update_id']) ) {
            return $this->parseTelegram();
        }

        if ( !empty($this->data['route']) ) {
            return $this->parseSiteRequest();
        }
    }

    private function parseTelegram() {
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

    private function parseSiteRequest() {
        $this->text = $this->data['route'];

        $raw = [];
        foreach ( $this->data as $key => $value ) {
            if ( $key === 'route' ) continue;
            if ( $key === '_files' ) continue;

            $raw[$key]  = $value;
            $this->$key = $value;
        }

        $this->request = (object) $raw;

        if ( !empty($this->data['_files']) ) {
            foreach ( $this->data['_files'] as $fileKey => $fileValue ) {
                $this->$fileKey          = $fileValue;
                $this->request->$fileKey = $fileValue;
            }
        }
    }

    public function getInput(): string {
        if ( $this->updateType === 'callback_query' ) return $this->callbackData ?? '';
        if ( $this->updateType === 'inline_query' ) return $this->query ?? '';
        return $this->text ?? '';
    }

    public function getUpdateType(): string {
        return $this->updateType ?? '';
    }

    public function getRawData(): array {
        return $this->data ?? [];
    }

    public function all(): array {
        return $this->data ?? [];
    }

    private function convertIndexedData( array $raw ): array {
        if ( array_keys($raw) !== range(0, count($raw) - 1) ) {
            return $raw;
        }

        $rebuilt = [];

        if ( isset($raw[0]) ) $rebuilt['chat_id'] = $raw[0];
        if ( isset($raw[1]) ) $rebuilt['caption'] = $raw[1];

        return $rebuilt;
    }

    private function rebuildDataKeys( array $data ): array {
        if ( array_keys($data) !== range(0, count($data) - 1) ) {
            return $data;
        }

        $expected = [ 'chat_id', 'caption' ];

        $rebuilt = [];

        foreach ( $data as $i => $value ) {
            $key           = $expected[$i] ?? $i;
            $rebuilt[$key] = $value;
        }

        return $rebuilt;
    }

    private function parseEditedmessage( array $m ) {
        $this->parseMessage($m);
    }

    private function parseMessage( array $m ) {
        $this->text       = $m['text'] ?? null;
        $this->chatID     = $m['chat']['id'] ?? null;
        $this->fromID     = $m['from']['id'] ?? null;
        $this->firstName  = $m['from']['first_name'] ?? null;
        $this->lastName   = $m['from']['last_name'] ?? null;
        $this->username   = $m['from']['username'] ?? null;
        $this->date       = $m['date'] ?? null;
        $this->message_id = $m['message_id'] ?? null;
        $this->entities   = $m['entities'] ?? null;
        $this->caption    = $m['caption'] ?? null;

        $this->photo            = $m['photo'] ?? null;
        $this->audio            = $m['audio'] ?? null;
        $this->document         = $m['document'] ?? null;
        $this->video            = $m['video'] ?? null;
        $this->voice            = $m['voice'] ?? null;
        $this->contact          = $m['contact'] ?? null;
        $this->location         = $m['location'] ?? null;
        $this->venue            = $m['venue'] ?? null;
        $this->sticker          = $m['sticker'] ?? null;
        $this->animation        = $m['animation'] ?? null;
        $this->dice             = $m['dice'] ?? null;
        $this->new_chat_members = $m['new_chat_members'] ?? null;
        $this->left_chat_member = $m['left_chat_member'] ?? null;
        $this->new_chat_title   = $m['new_chat_title'] ?? null;
        $this->new_chat_photo   = $m['new_chat_photo'] ?? null;
        $this->pinned_message   = $m['pinned_message'] ?? null;
        $this->reply_to_message = $m['reply_to_message'] ?? null;
    }

    private function parseChannelpost( array $m ) {
        $this->parseMessage($m);
    }

    private function parseEditedchannelpost( array $m ) {
        $this->parseMessage($m);
    }

    private function parseCallbackquery( array $c ) {
        $this->query_id     = $c['id'] ?? null;
        $this->callbackData = $c['data'] ?? null;
        $this->chatID       = $c['message']['chat']['id'] ?? null;
        $this->message_id   = $c['message']['message_id'] ?? null;

        $this->fromID    = $c['from']['id'] ?? null;
        $this->firstName = $c['from']['first_name'] ?? null;
        $this->lastName  = $c['from']['last_name'] ?? null;
        $this->username  = $c['from']['username'] ?? null;

        $this->text = $this->callbackData;
    }

    private function parseInlinequery( array $q ) {
        $this->inline_query_id = $q['id'] ?? null;
        $this->query           = $q['query'] ?? null;
        $this->offset          = $q['offset'] ?? null;

        $this->fromID    = $q['from']['id'] ?? null;
        $this->firstName = $q['from']['first_name'] ?? null;
        $this->lastName  = $q['from']['last_name'] ?? null;
        $this->username  = $q['from']['username'] ?? null;
    }

    private function parseChoseninlineresult( array $r ) {
        $this->fromID    = $r['from']['id'] ?? null;
        $this->firstName = $r['from']['first_name'] ?? null;
        $this->lastName  = $r['from']['last_name'] ?? null;
        $this->username  = $r['from']['username'] ?? null;

        $this->query     = $r['query'] ?? null;
        $this->result_id = $r['result_id'] ?? null;
    }

    private function parseShippingquery( array $sq ) {
        $this->shipping_query_id = $sq['id'] ?? null;
        $this->fromID            = $sq['from']['id'] ?? null;
        $this->invoice_payload   = $sq['invoice_payload'] ?? null;
        $this->shipping_address  = $sq['shipping_address'] ?? null;
    }

    private function parsePrecheckoutquery( array $p ) {
        $this->pre_checkout_query_id = $p['id'] ?? null;
        $this->fromID                = $p['from']['id'] ?? null;

        $this->currency        = $p['currency'] ?? null;
        $this->total_amount    = $p['total_amount'] ?? null;
        $this->invoice_payload = $p['invoice_payload'] ?? null;
        $this->order_info      = $p['order_info'] ?? null;
    }

    private function parsePoll( array $poll ) {
        $this->poll_id           = $poll['id'] ?? null;
        $this->question          = $poll['question'] ?? null;
        $this->options           = $poll['options'] ?? null;
        $this->total_voter_count = $poll['total_voter_count'] ?? null;
        $this->is_closed         = $poll['is_closed'] ?? null;
        $this->is_anonymous      = $poll['is_anonymous'] ?? null;
    }

    private function parsePollanswer( array $pa ) {
        $this->poll_id    = $pa['poll_id'] ?? null;
        $this->fromID     = $pa['user']['id'] ?? null;
        $this->option_ids = $pa['option_ids'] ?? null;
    }

    private function parseChatmember( array $cm ) {
        $this->parseMychatmember($cm);
    }

    private function parseMychatmember( array $cm ) {
        $this->chatID = $cm['chat']['id'] ?? null;
        $this->fromID = $cm['from']['id'] ?? null;
        $this->date   = $cm['date'] ?? null;

        $this->old_chat_member = $cm['old_chat_member'] ?? null;
        $this->new_chat_member = $cm['new_chat_member'] ?? null;
    }

    private function parseChatjoinrequest( array $cr ) {
        $this->chatID = $cr['chat']['id'] ?? null;
        $this->fromID = $cr['from']['id'] ?? null;
        $this->date   = $cr['date'] ?? null;

        $this->bio         = $cr['bio'] ?? null;
        $this->invite_link = $cr['invite_link'] ?? null;
    }
}
