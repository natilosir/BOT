<?php

namespace natilosir\bot\Bot\Drivers\Telegram\Traits;

use Illuminate\Support\Arr;

trait CoreTrait {
    public function getMe( array $data = [] ) {
        return $this->api('getMe', $data);
    }

    public function logOut( array $data = [] ) {
        return $this->api('logOut', $data);
    }

    public function close( array $data = [] ) {
        return $this->api('close', $data);
    }

    public function getUpdates( $offsetOrData = null, $limit = null, $timeout = null, $allowedUpdates = null ) {
        if ( is_array($offsetOrData) ) {
            return $this->api('getUpdates', $offsetOrData);
        }

        $data = [];
        Arr::set($data, 'offset', $offsetOrData);
        Arr::set($data, 'limit', $limit);
        Arr::set($data, 'timeout', $timeout);
        Arr::set($data, 'allowed_updates', $allowedUpdates);

        return $this->api('getUpdates', $data);
    }

    public function setWebhook( $urlOrData = null, $certificate = null, $ipAddress = null, $maxConnections = null, $allowedUpdates = null, $dropPendingUpdates = null, $secretToken = null ) {
        if ( is_array($urlOrData) ) {
            $data = $urlOrData;

            if ( !array_key_exists('secret_token', $data) ) {
                $configuredSecret = (string) $this->config('webhook.secret_token', '');
                if ( $configuredSecret !== '' ) {
                    $data['secret_token'] = $configuredSecret;
                }
            }

            return $this->api('setWebhook', $data);
        }

        $configuredUrl = (string) $this->config('webhook.url', '');
        $configuredSecret = (string) $this->config('webhook.secret_token', '');

        $data = [ 'url' => $urlOrData ?? $configuredUrl ];
        Arr::set($data, 'certificate', $certificate);
        Arr::set($data, 'ip_address', $ipAddress);
        Arr::set($data, 'max_connections', $maxConnections);
        Arr::set($data, 'allowed_updates', $allowedUpdates);
        Arr::set($data, 'drop_pending_updates', $dropPendingUpdates);
        Arr::set($data, 'secret_token', $secretToken ?? ($configuredSecret !== '' ? $configuredSecret : null));

        return $this->api('setWebhook', $data);
    }

    public function deleteWebhook( $dropPendingUpdatesOrData = null ) {
        if ( is_array($dropPendingUpdatesOrData) ) {
            return $this->api('deleteWebhook', $dropPendingUpdatesOrData);
        }

        $data = [];
        Arr::set($data, 'drop_pending_updates', $dropPendingUpdatesOrData);
        return $this->api('deleteWebhook', $data);
    }

    public function getWebhookInfo( array $data = [] ) {
        return $this->api('getWebhookInfo', $data);
    }

    public function getFile( $fileIdOrData = null ) {
        if ( is_array($fileIdOrData) ) {
            return $this->api('getFile', $fileIdOrData);
        }

        return $this->api('getFile', [ 'file_id' => $fileIdOrData ]);
    }

    public function getUserProfilePhotos( $userIdOrData = null, $offset = null, $limit = null ) {
        if ( is_array($userIdOrData) ) {
            return $this->api('getUserProfilePhotos', $userIdOrData);
        }

        $data = [ 'user_id' => $userIdOrData ];
        Arr::set($data, 'offset', $offset);
        Arr::set($data, 'limit', $limit);
        return $this->api('getUserProfilePhotos', $data);
    }


    public function getUserProfileAudios( $userIdOrData = null, $offset = null, $limit = null ) {
        if ( is_array($userIdOrData) ) {
            return $this->api('getUserProfileAudios', $userIdOrData);
        }

        $data = [ 'user_id' => $userIdOrData ];
        Arr::set($data, 'offset', $offset);
        Arr::set($data, 'limit', $limit);
        return $this->api('getUserProfileAudios', $data);
    }
}
