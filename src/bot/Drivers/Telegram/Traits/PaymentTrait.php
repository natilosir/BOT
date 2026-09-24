<?php

namespace natilosir\bot\Bot\Drivers\Telegram\Traits;

use Illuminate\Support\Arr;

trait PaymentTrait {
    /**
     * Send an invoice with the main required fields explicit.
     * $options may include message_thread_id, direct_messages_topic_id,
     * max_tip_amount, suggested_tip_amounts, start_parameter, provider_data,
     * photo_url/photo_size/photo_width/photo_height, need_name,
     * need_phone_number, need_email, need_shipping_address,
     * send_phone_number_to_provider, send_email_to_provider, is_flexible,
     * disable_notification, protect_content, allow_paid_broadcast,
     * message_effect_id, suggested_post_parameters, reply_parameters,
     * reply_markup and any future Telegram field.
     */
    public function sendInvoice( $chatIdOrData, $title = null, $description = null, $payload = null, $currency = null, $prices = null, $providerToken = null, array $options = [] ) {
        if ( is_array($chatIdOrData) ) return $this->api('sendInvoice', $chatIdOrData);

        $data = [
            'chat_id'     => $chatIdOrData,
            'title'       => $title,
            'description' => $description,
            'payload'     => $payload,
        ];
        Arr::set($data, 'provider_token', $providerToken);
        $data['currency'] = $currency;
        $data['prices']   = $prices;

        return $this->api('sendInvoice', $this->withExtra($data, $options));
    }

    public function createInvoiceLink( ...$args ) {
        return $this->apiFromArguments('createInvoiceLink', $args);
    }

    public function answerShippingQuery( $shippingQueryIdOrData, $ok = null, $shippingOptions = null, $errorMessage = null ) {
        if ( is_array($shippingQueryIdOrData) ) return $this->api('answerShippingQuery', $shippingQueryIdOrData);
        $data = [ 'shipping_query_id' => $shippingQueryIdOrData, 'ok' => $ok ];
        Arr::set($data, 'shipping_options', $shippingOptions);
        Arr::set($data, 'error_message', $errorMessage);
        return $this->api('answerShippingQuery', $data);
    }

    public function answerPreCheckoutQuery( $preCheckoutQueryIdOrData, $ok = null, $errorMessage = null ) {
        if ( is_array($preCheckoutQueryIdOrData) ) return $this->api('answerPreCheckoutQuery', $preCheckoutQueryIdOrData);
        $data = [ 'pre_checkout_query_id' => $preCheckoutQueryIdOrData, 'ok' => $ok ];
        Arr::set($data, 'error_message', $errorMessage);
        return $this->api('answerPreCheckoutQuery', $data);
    }

    public function getMyStarBalance( array $data = [] ) {
        return $this->api('getMyStarBalance', $data);
    }

    public function getStarTransactions( array $data = [] ) {
        return $this->api('getStarTransactions', $data);
    }

    public function refundStarPayment( ...$args ) {
        return $this->apiFromArguments('refundStarPayment', $args);
    }

    public function editUserStarSubscription( ...$args ) {
        return $this->apiFromArguments('editUserStarSubscription', $args);
    }

    public function setPassportDataErrors( ...$args ) {
        return $this->apiFromArguments('setPassportDataErrors', $args);
    }
}
