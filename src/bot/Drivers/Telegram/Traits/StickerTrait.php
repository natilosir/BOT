<?php

namespace natilosir\bot\Bot\Drivers\Telegram\Traits;

trait StickerTrait {
    public function getStickerSet( ...$args ) {
        return $this->apiFromArguments('getStickerSet', $args);
    }

    public function getCustomEmojiStickers( ...$args ) {
        return $this->apiFromArguments('getCustomEmojiStickers', $args);
    }

    public function uploadStickerFile( ...$args ) {
        return $this->apiFromArguments('uploadStickerFile', $args);
    }

    public function createNewStickerSet( ...$args ) {
        return $this->apiFromArguments('createNewStickerSet', $args);
    }

    public function addStickerToSet( ...$args ) {
        return $this->apiFromArguments('addStickerToSet', $args);
    }

    public function setStickerPositionInSet( ...$args ) {
        return $this->apiFromArguments('setStickerPositionInSet', $args);
    }

    public function deleteStickerFromSet( ...$args ) {
        return $this->apiFromArguments('deleteStickerFromSet', $args);
    }

    public function replaceStickerInSet( ...$args ) {
        return $this->apiFromArguments('replaceStickerInSet', $args);
    }

    public function setStickerEmojiList( ...$args ) {
        return $this->apiFromArguments('setStickerEmojiList', $args);
    }

    public function setStickerKeywords( ...$args ) {
        return $this->apiFromArguments('setStickerKeywords', $args);
    }

    public function setStickerMaskPosition( ...$args ) {
        return $this->apiFromArguments('setStickerMaskPosition', $args);
    }

    public function setStickerSetTitle( ...$args ) {
        return $this->apiFromArguments('setStickerSetTitle', $args);
    }

    public function setStickerSetThumbnail( ...$args ) {
        return $this->apiFromArguments('setStickerSetThumbnail', $args);
    }

    public function setCustomEmojiStickerSetThumbnail( ...$args ) {
        return $this->apiFromArguments('setCustomEmojiStickerSetThumbnail', $args);
    }

    public function deleteStickerSet( ...$args ) {
        return $this->apiFromArguments('deleteStickerSet', $args);
    }
}
