<?php

namespace natilosir\bot\bot\Traits;

trait BusinessTrait {
    public function getBusinessConnection( ...$args ) {
        $data = $this->buildApiData($args);
        return $this->api('getBusinessConnection', $data);
    }

    public function readBusinessMessage( ...$args ) {
        $data = $this->buildApiData($args);
        return $this->api('readBusinessMessage', $data);
    }

    public function deleteBusinessMessages( ...$args ) {
        $data = $this->buildApiData($args);
        return $this->api('deleteBusinessMessages', $data);
    }

    public function setBusinessAccountName( ...$args ) {
        $data = $this->buildApiData($args);
        return $this->api('setBusinessAccountName', $data);
    }

    public function setBusinessAccountUsername( ...$args ) {
        $data = $this->buildApiData($args);
        return $this->api('setBusinessAccountUsername', $data);
    }

    public function setBusinessAccountBio( ...$args ) {
        $data = $this->buildApiData($args);
        return $this->api('setBusinessAccountBio', $data);
    }

    public function setBusinessAccountProfilePhoto( ...$args ) {
        $data = $this->buildApiData($args);
        return $this->api('setBusinessAccountProfilePhoto', $data);
    }

    public function removeBusinessAccountProfilePhoto( ...$args ) {
        $data = $this->buildApiData($args);
        return $this->api('removeBusinessAccountProfilePhoto', $data);
    }

    public function setBusinessAccountGiftSettings( ...$args ) {
        $data = $this->buildApiData($args);
        return $this->api('setBusinessAccountGiftSettings', $data);
    }

    public function getBusinessAccountStarBalance( ...$args ) {
        $data = $this->buildApiData($args);
        return $this->api('getBusinessAccountStarBalance', $data);
    }

    public function transferBusinessAccountStars( ...$args ) {
        $data = $this->buildApiData($args);
        return $this->api('transferBusinessAccountStars', $data);
    }

    public function getBusinessAccountGifts( ...$args ) {
        $data = $this->buildApiData($args);
        return $this->api('getBusinessAccountGifts', $data);
    }

    public function convertGiftToStars( ...$args ) {
        $data = $this->buildApiData($args);
        return $this->api('convertGiftToStars', $data);
    }

    public function upgradeGift( ...$args ) {
        $data = $this->buildApiData($args);
        return $this->api('upgradeGift', $data);
    }

    public function transferGift( ...$args ) {
        $data = $this->buildApiData($args);
        return $this->api('transferGift', $data);
    }

    public function postStory( ...$args ) {
        $data = $this->buildApiData($args);
        return $this->api('postStory', $data);
    }

    public function repostStory( ...$args ) {
        $data = $this->buildApiData($args);
        return $this->api('repostStory', $data);
    }

    public function editStory( ...$args ) {
        $data = $this->buildApiData($args);
        return $this->api('editStory', $data);
    }

    public function deleteStory( ...$args ) {
        $data = $this->buildApiData($args);
        return $this->api('deleteStory', $data);
    }
}
