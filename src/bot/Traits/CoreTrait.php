<?php

namespace natilosir\bot\bot\Traits;

trait CoreTrait {
    private function buildApiData( array $args ): array {
        if ( isset($args[0]) && is_array($args[0]) ) {
            return $args[0];
        }
        $data = [];
        foreach ( $args as $i => $value ) {
            $data["param_" . $i] = $value;
        }
        return $data;
    }

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
        $this->addOptional($data, 'offset', $offsetOrData);
        $this->addOptional($data, 'limit', $limit);
        $this->addOptional($data, 'timeout', $timeout);
        $this->addOptional($data, 'allowed_updates', $allowedUpdates);

        return $this->api('getUpdates', $data);
    }

    public function setWebhook( $urlOrData = null, $certificate = null, $ipAddress = null, $maxConnections = null, $allowedUpdates = null, $dropPendingUpdates = null, $secretToken = null ) {
        if ( is_array($urlOrData) ) {
            return $this->api('setWebhook', $urlOrData);
        }

        $data = [ 'url' => $urlOrData ?? '' ];
        $this->addOptional($data, 'certificate', $certificate);
        $this->addOptional($data, 'ip_address', $ipAddress);
        $this->addOptional($data, 'max_connections', $maxConnections);
        $this->addOptional($data, 'allowed_updates', $allowedUpdates);
        $this->addOptional($data, 'drop_pending_updates', $dropPendingUpdates);
        $this->addOptional($data, 'secret_token', $secretToken);

        return $this->api('setWebhook', $data);
    }

    public function deleteWebhook( $dropPendingUpdatesOrData = null ) {
        if ( is_array($dropPendingUpdatesOrData) ) {
            return $this->api('deleteWebhook', $dropPendingUpdatesOrData);
        }

        $data = [];
        $this->addOptional($data, 'drop_pending_updates', $dropPendingUpdatesOrData);
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
        $this->addOptional($data, 'offset', $offset);
        $this->addOptional($data, 'limit', $limit);
        return $this->api('getUserProfilePhotos', $data);
    }
}
