<?php

namespace natilosir\bot\Bot\Drivers\Bale;

/** Official Bale Bot API method catalog. */
final class BaleMethodCatalog {
    public const ALL = [
        'getUpdates',
        'setWebhook',
        'deleteWebhook',
        'getWebhookInfo',
        'getMe',
        'sendMessage',
        'forwardMessage',
        'copyMessage',
        'sendPhoto',
        'sendAudio',
        'sendDocument',
        'sendVideo',
        'sendAnimation',
        'sendVoice',
        'sendMediaGroup',
        'sendLocation',
        'sendContact',
        'sendChatAction',
        'getFile',
        'answerCallbackQuery',
        'askReview',
        'banChatMember',
        'unbanChatMember',
        'promoteChatMember',
        'setChatPhoto',
        'leaveChat',
        'getChat',
        'getChatAdministrators',
        'getChatMembersCount',
        'getChatMember',
        'pinChatMessage',
        'unPinChatMessage',
        'unpinAllChatMessages',
        'setChatTitle',
        'setChatDescription',
        'deleteChatPhoto',
        'createChatInviteLink',
        'revokeChatInviteLink',
        'exportChatInviteLink',
        'editMessageText',
        'editMessageCaption',
        'editMessageReplyMarkup',
        'deleteMessage',
        'uploadStickerFile',
        'createNewStickerSet',
        'addStickerToSet',
        'sendInvoice',
        'createInvoiceLink',
        'answerPreCheckoutQuery',
        'inquireTransaction',
    ];

    public const BUSINESS = [
        'getMe',
        'sendMessage',
        'forwardMessage',
        'sendPhoto',
        'sendVideo',
        'sendAudio',
        'sendDocument',
    ];

    private function __construct() {}
}
