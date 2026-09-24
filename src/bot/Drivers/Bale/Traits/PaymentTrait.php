<?php

namespace natilosir\bot\Bot\Drivers\Bale\Traits;

use Illuminate\Support\Arr;

trait PaymentTrait {
    public function sendInvoice( $chatIdOrData, $title = null, $description = null, $payload = null, $providerToken = null, $prices = null, $photoUrl = null, $replyToMessageId = null ): mixed {
        if ( is_array($chatIdOrData) ) return $this->api('sendInvoice', $chatIdOrData);
        $data = [
            'chat_id'        => $chatIdOrData,
            'title'          => $title,
            'description'    => $description,
            'payload'        => $payload,
            'provider_token' => $providerToken,
            'prices'         => $prices,
        ];
        Arr::set($data, 'photo_url', $photoUrl);
        Arr::set($data, 'reply_to_message_id', $replyToMessageId);
        return $this->api('sendInvoice', $data);
    }

    public function createInvoiceLink( $titleOrData, $description = null, $payload = null, $providerToken = null, $prices = null ): mixed {
        if ( is_array($titleOrData) ) return $this->api('createInvoiceLink', $titleOrData);
        return $this->api('createInvoiceLink', [
            'title'          => $titleOrData,
            'description'    => $description,
            'payload'        => $payload,
            'provider_token' => $providerToken,
            'prices'         => $prices,
        ]);
    }

    public function answerPreCheckoutQuery( $preCheckoutQueryIdOrData, $ok = null, $errorMessage = null ): mixed {
        if ( is_array($preCheckoutQueryIdOrData) ) return $this->api('answerPreCheckoutQuery', $preCheckoutQueryIdOrData);
        $data = [ 'pre_checkout_query_id' => $preCheckoutQueryIdOrData, 'ok' => $ok ];
        Arr::set($data, 'error_message', $errorMessage);
        return $this->api('answerPreCheckoutQuery', $data);
    }

    public function inquireTransaction( $transactionIdOrData ): mixed {
        if ( is_array($transactionIdOrData) ) return $this->api('inquireTransaction', $transactionIdOrData);
        return $this->api('inquireTransaction', [ 'transaction_id' => $transactionIdOrData ]);
    }
}
