<?php

preg_match('/start=([a-zA-Z0-9_]+)/', $text, $matches);

if (isset($matches[1])) {
    $startValue = $matches[1];
}

$the_user = $user->where('tel_id', $fromID)->first();

if ($the_user->link_token) {
    $link = $the_user->link_token;
} else {
    $link = bin2hex(openssl_random_pseudo_bytes(5));
    $user->update($the_user->id, [
        'link_token' => $link,
        'created_at' => time(),
    ]);
}

$getChat        = http('getMe');
$anonymous_link = 'https://t.me/'.$getChat->result->username."?start=$link";

bot::sendMessage($fromID, "سلام $firstName هستم ✋️\n\n
لینک زیر رو لمس کن و هر حرفی که تو دلت هست یا هر انتقادی که نسبت به من داری رو با خیال راحت بنویس و بفرست. مطمئن باش که پیام‌هات بدون اینکه از اسمت باخبر بشم به دستم می‌رسه. خودت هم میتونی امتحان کنی و از بقیه بخوای راحت و ناشناس بهت پیام بفرستن. قول می‌دم حرفای خیلی جالبی بشنوی! 😉\n
👇👇\n
$anonymous_link
", $message_id);

$secmes = '<b>☝️ پیام بالا یا لینک داخلش رو در شبکه‌های اجتماعی به اشتراک بذار تا بقیه بتونن بهت پیام ناشناس بفرستن. پیام‌ها از طریق همین برنامه به دستت می‌رسه.

نکته: اگه فردی که بهت پیام ناشناس میده قبلاً توی ربات نبوده و عضو جدید باشه، بابتش امتیاز می‌گیری !🤩
</b>
👈 برای آموزش گذاشتن لینک ناشناس در شبکه‌های اجتماعی مختلف دستور /SocialLink رو لمس کن.

👈 اگه میخوای لینک ناشناست رو در مکان‌های واقعی و عمومی به اشتراک بذاری دستور /QRLink رو لمس کن.😎';

bot::sendMessage($fromID, $secmes, $message_id + 1);
