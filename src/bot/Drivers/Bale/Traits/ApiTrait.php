<?php

namespace natilosir\bot\Bot\Drivers\Bale\Traits;

trait ApiTrait {
    public function api( string $method, array $data = [], string $httpMethod = 'POST' ): mixed {
        if ( strcasecmp($method, 'getChatMemberCount') === 0 ) {
            $method = 'getChatMembersCount';
        }

        if ( in_array(strtolower($method), [
            'sendmessage',
            'sendphoto',
            'sendaudio',
            'senddocument',
            'sendvideo',
            'sendanimation',
            'sendvoice',
        ], true) ) {
            unset($data['parse_mode']);
        }

        if ( strcasecmp($method, 'sendInvoice') === 0
             || strcasecmp($method, 'createInvoiceLink') === 0 ) {
            unset($data['currency']);
        }

        return parent::api($method, $data, $httpMethod);
    }

    public function request( string $method, array $data = [], string $httpMethod = 'POST' ): mixed {
        return $this->api($method, $data, $httpMethod);
    }

    public function file( string $path, ?string $name = null ): array {
        return [
            'tmp_name' => $path,
            'name'     => $name ? : basename($path),
        ];
    }

    public function __call( string $name, array $arguments ): mixed {
        $data       = isset($arguments[0]) && is_array($arguments[0]) ? $arguments[0] : [];
        $httpMethod = isset($arguments[1]) && is_string($arguments[1]) ? $arguments[1] : 'POST';

        return $this->api($name, $data, $httpMethod);
    }
}
