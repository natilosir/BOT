<?php

namespace natilosir\bot\Bot\Drivers\Telegram\Traits;

trait StickerTrait {
    public function getStickerSet( ...$args ) {
        $data = $this->buildApiData($args);
        return $this->api('getStickerSet', $data);
    }

    public function getCustomEmojiStickers( ...$args ) {
        $data = $this->buildApiData($args);
        return $this->api('getCustomEmojiStickers', $data);
    }

    public function uploadStickerFile( ...$args ) {

        $data = $this->buildApiData($args);
        return $this->api('uploadStickerFile', $data);
    }

    public function createNewStickerSet( ...$args ) {

        $data = $this->buildApiData($args);
        return $this->api('createNewStickerSet', $data);
    }

    public function addStickerToSet( ...$args ) {

        $data = $this->buildApiData($args);
        return $this->api('addStickerToSet', $data);
    }

    public function setStickerPositionInSet( ...$args ) {
        $data = $this->buildApiData($args);
        return $this->api('setStickerPositionInSet', $data);
    }

    public function deleteStickerFromSet( ...$args ) {
        $data = $this->buildApiData($args);
        return $this->api('deleteStickerFromSet', $data);
    }

    public function replaceStickerInSet( ...$args ) {
        $data = $this->buildApiData($args);
        return $this->api('replaceStickerInSet', $data);
    }

    public function setStickerEmojiList( ...$args ) {
        $data = $this->buildApiData($args);
        return $this->api('setStickerEmojiList', $data);
    }

    public function setStickerKeywords( ...$args ) {
        $data = $this->buildApiData($args);
        return $this->api('setStickerKeywords', $data);
    }

    public function setStickerMaskPosition( ...$args ) {
        $data = $this->buildApiData($args);
        return $this->api('setStickerMaskPosition', $data);
    }

    public function setStickerSetTitle( ...$args ) {
        $data = $this->buildApiData($args);
        return $this->api('setStickerSetTitle', $data);
    }

    public function setStickerSetThumbnail( ...$args ) {
        $data = $this->buildApiData($args);
        return $this->api('setStickerSetThumbnail', $data);
    }

    public function setCustomEmojiStickerSetThumbnail( ...$args ) {
        $data = $this->buildApiData($args);
        return $this->api('setCustomEmojiStickerSetThumbnail', $data);
    }

    public function deleteStickerSet( ...$args ) {
        $data = $this->buildApiData($args);
        return $this->api('deleteStickerSet', $data);
    }
}
