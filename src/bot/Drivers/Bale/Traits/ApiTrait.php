<?php

namespace natilosir\bot\Bot\Drivers\Bale\Traits;

use natilosir\bot\Bot\Drivers\Concerns\InteractsWithApi;

trait ApiTrait {
    use InteractsWithApi;

    private const METHOD_ALIASES = [
        'getchatmembercount' => 'getChatMembersCount',
    ];

    private const METHODS_WITHOUT_PARSE_MODE = [
        'sendmessage',
        'sendphoto',
        'sendaudio',
        'senddocument',
        'sendvideo',
        'sendanimation',
        'sendvoice',
    ];

    private const METHODS_WITHOUT_CURRENCY = [
        'sendinvoice',
        'createinvoicelink',
    ];

    public function api( string $method, array $data = [], string $httpMethod = 'POST' ): mixed {
        $normalized = strtolower($method);
        $method     = self::METHOD_ALIASES[$normalized] ?? $method;
        $normalized = strtolower($method);

        if ( in_array($normalized, self::METHODS_WITHOUT_PARSE_MODE, true) ) {
            unset($data['parse_mode']);
        }

        if ( in_array($normalized, self::METHODS_WITHOUT_CURRENCY, true) ) {
            unset($data['currency']);
        }

        return parent::api($method, $data, $httpMethod);
    }
}
