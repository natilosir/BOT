<?php

$newmsg = DB::table('msg')->where(['receiver' => $fromID, 'see' => 0])->orderby('id', 'asc')->get();

function copyAndDeleteMessage($fromID, $msg)
{
    bot::row([
        bot::column('⛔️ بلاک', 'block_'.$msg->receiver.'_'.$msg->sender.'_'.$msg->message_id),
        bot::column('✍️ پاسخ', 'reply_'.$msg->receiver.'_'.$msg->sender.'_'.$msg->message_id),
    ]);

    $tel_response = bot::inline($fromID, $msg->sender, $msg->message_id, true);
    if (isset($tel_response->ok)) {
        bot::sendMessage($msg->sender, '☝️🏼 این پیامت رو دید!', $msg->message_id);
        DB::table('msg')->delete($msg->id);
    }
    bot::clearCache();
}

if (count($newmsg) > 0) {
    if (count($newmsg) == 1) {
        copyAndDeleteMessage($fromID, $newmsg);
    } else {
        foreach ($newmsg as $msg) {
            copyAndDeleteMessage($fromID, $msg);
        }
    }
} else {
    bot::sendMessage($chatID, 'لیست پیام های شما خالی میباشد !', $message_id);
}
