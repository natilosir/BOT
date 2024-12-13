<?php

preg_match('/start=([a-zA-Z0-9_]+)/', $text, $matches);

if (isset($matches[1])) {
    $startValue = $matches[1];
}

$user = DB::table('users')->where('tel_id', $fromID)->first();

if ($user->link_token) {
    $link = $user->link_token;
} else {
    $link = substr(md5(rand(999, 999999999)), 2, 10);
    DB::table('users')->where('id', $user->id)->update([
        'link_token' => $link,
        'created_at' => time(),
    ]);
}

$getChat        = http('getMe');
$anonymous_link = 'https://t.me/'.$getChat->result->username."?start=$link";

bot::sendMessage($fromID, " حوصلت سر رفته؟ 😀\n\nمیخوای به آدمای اطرافت وصل بشی؟ 😎\n\nمیخوای به هر کسی دلت خواست وصل بشی و حرفای دِلتو بصورت کاملاً ناشناس بهش بگی ؟\n\n➕ بیا اینجا کلی دوست پیدا کن !\n\nشروع کن 👇\n\n ".$anonymous_link, $message_id);

$secmes = 'بنر بالا رو بفرست به دوستات و به ازای هر زیرمجموعه 30 عدد سکه بگیر 🫰🏻';
bot::sendMessage($fromID, $secmes, $message_id + 1);
