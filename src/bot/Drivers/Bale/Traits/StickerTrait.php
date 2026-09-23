<?php

namespace natilosir\bot\Bot\Drivers\Bale\Traits;

trait StickerTrait {
    public function uploadStickerFile( $userIdOrData, $sticker = null ): mixed {
        if ( is_array($userIdOrData) ) return $this->api('uploadStickerFile', $userIdOrData);
        return $this->api('uploadStickerFile', [ 'user_id' => $userIdOrData, 'sticker' => $sticker ]);
    }

    public function createNewStickerSet( $userIdOrData, $name = null, $title = null, $sticker = null ): mixed {
        if ( is_array($userIdOrData) ) return $this->api('createNewStickerSet', $userIdOrData);
        return $this->api('createNewStickerSet', [
            'user_id' => $userIdOrData,
            'name'    => $name,
            'title'   => $title,
            'sticker' => $sticker,
        ]);
    }

    public function addStickerToSet( $userIdOrData, $name = null, $sticker = null ): mixed {
        if ( is_array($userIdOrData) ) return $this->api('addStickerToSet', $userIdOrData);
        return $this->api('addStickerToSet', [ 'user_id' => $userIdOrData, 'name' => $name, 'sticker' => $sticker ]);
    }
}
