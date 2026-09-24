<?php

namespace natilosir\bot\Bot\Drivers\Bale\Traits;

use Illuminate\Support\Arr;

trait CoreTrait {
    public function getUpdates( $offsetOrData = null, $limit = null, $timeout = null ): mixed {
        if ( is_array($offsetOrData) ) return $this->api('getUpdates', $offsetOrData);
        $data = [];
        Arr::set($data, 'offset', $offsetOrData);
        Arr::set($data, 'limit', $limit);
        Arr::set($data, 'timeout', $timeout);
        return $this->api('getUpdates', $data);
    }

    public function setWebhook( $urlOrData = null ): mixed {
        if ( is_array($urlOrData) ) {
            return $this->api('setWebhook', $urlOrData);
        }

        $configuredUrl = (string) $this->config('webhook.url', '');

        return $this->api('setWebhook', [
            'url' => $urlOrData ?? $configuredUrl,
        ]);
    }

    public function deleteWebhook( array $data = [] ): mixed {
        return $this->api('deleteWebhook', $data);
    }

    public function getWebhookInfo( array $data = [] ): mixed {
        return $this->api('getWebhookInfo', $data);
    }

    public function getMe( array $data = [] ): mixed {
        return $this->api('getMe', $data);
    }

    public function getFile( $fileIdOrData ): mixed {
        if ( is_array($fileIdOrData) ) return $this->api('getFile', $fileIdOrData);
        return $this->api('getFile', [ 'file_id' => $fileIdOrData ]);
    }

    public function askReview( $userIdOrData, $delaySeconds = null ): mixed {
        if ( is_array($userIdOrData) ) return $this->api('askReview', $userIdOrData);
        return $this->api('askReview', [ 'user_id' => $userIdOrData, 'delay_seconds' => $delaySeconds ]);
    }
}
