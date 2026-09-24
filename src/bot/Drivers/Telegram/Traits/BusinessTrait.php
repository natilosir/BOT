<?php

namespace natilosir\bot\Bot\Drivers\Telegram\Traits;

trait BusinessTrait {
    public function getBusinessConnection( ...$args ) {
        return $this->apiFromArguments('getBusinessConnection', $args);
    }

    public function readBusinessMessage( ...$args ) {
        return $this->apiFromArguments('readBusinessMessage', $args);
    }

    public function deleteBusinessMessages( ...$args ) {
        return $this->apiFromArguments('deleteBusinessMessages', $args);
    }

    public function setBusinessAccountName( ...$args ) {
        return $this->apiFromArguments('setBusinessAccountName', $args);
    }

    public function setBusinessAccountUsername( ...$args ) {
        return $this->apiFromArguments('setBusinessAccountUsername', $args);
    }

    public function setBusinessAccountBio( ...$args ) {
        return $this->apiFromArguments('setBusinessAccountBio', $args);
    }

    public function setBusinessAccountProfilePhoto( ...$args ) {
        return $this->apiFromArguments('setBusinessAccountProfilePhoto', $args);
    }

    public function removeBusinessAccountProfilePhoto( ...$args ) {
        return $this->apiFromArguments('removeBusinessAccountProfilePhoto', $args);
    }

    public function setBusinessAccountGiftSettings( ...$args ) {
        return $this->apiFromArguments('setBusinessAccountGiftSettings', $args);
    }

    public function getBusinessAccountStarBalance( ...$args ) {
        return $this->apiFromArguments('getBusinessAccountStarBalance', $args);
    }

    public function transferBusinessAccountStars( ...$args ) {
        return $this->apiFromArguments('transferBusinessAccountStars', $args);
    }

    public function getBusinessAccountGifts( ...$args ) {
        return $this->apiFromArguments('getBusinessAccountGifts', $args);
    }

    public function convertGiftToStars( ...$args ) {
        return $this->apiFromArguments('convertGiftToStars', $args);
    }

    public function upgradeGift( ...$args ) {
        return $this->apiFromArguments('upgradeGift', $args);
    }

    public function transferGift( ...$args ) {
        return $this->apiFromArguments('transferGift', $args);
    }

    public function postStory( ...$args ) {
        return $this->apiFromArguments('postStory', $args);
    }

    public function repostStory( ...$args ) {
        return $this->apiFromArguments('repostStory', $args);
    }

    public function editStory( ...$args ) {
        return $this->apiFromArguments('editStory', $args);
    }

    public function deleteStory( ...$args ) {
        return $this->apiFromArguments('deleteStory', $args);
    }
}
