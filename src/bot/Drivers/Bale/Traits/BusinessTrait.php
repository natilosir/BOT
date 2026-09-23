<?php

namespace natilosir\bot\Bot\Drivers\Bale\Traits;

trait BusinessTrait {
    public function businessApi( string $method, array $data = [], string $httpMethod = 'POST' ): mixed {
        $url = $this->apiBaseUrl . '/business/bot' . $this->botToken . '/' . ltrim($method, '/');
        return $this->requestTo($url, $data, $httpMethod);
    }

    public function businessGetMe( array $data = [] ): mixed {
        return $this->businessApi('getMe', $data);
    }

    public function businessSendMessage( array $data ): mixed {
        return $this->businessApi('sendMessage', $data);
    }

    public function businessForwardMessage( array $data ): mixed {
        return $this->businessApi('forwardMessage', $data);
    }

    public function businessSendPhoto( array $data ): mixed {
        return $this->businessApi('sendPhoto', $data);
    }

    public function businessSendVideo( array $data ): mixed {
        return $this->businessApi('sendVideo', $data);
    }

    public function businessSendAudio( array $data ): mixed {
        return $this->businessApi('sendAudio', $data);
    }

    public function businessSendDocument( array $data ): mixed {
        return $this->businessApi('sendDocument', $data);
    }
}
