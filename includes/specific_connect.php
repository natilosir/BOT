<?php

// Fetch the current user
$the_user = $user->where('tel_id', $fromID)->first();

$nachricht = '<b>برای اینکه بتونم به مخاطب خاصت بطور ناشناس وصلت کنم ، یکی از کارای زیر رو انجام بده:</b>

راه اول 👈 : Username@ یا همون آی‌دی تلگرام اون شخص رو الان وارد ربات کن !

راه دوم 👈 : یه پیام متنی از کسی که می‌خوای بهش پیام ناشناس بفرستی رو الان به این ربات فوروارد کن تا ببینم عضو هست یا نه !

راه سوم 👈 : شماره یا همون Contact مخاطبت رو ارسال کن توی ربات ، تا ببینیم عضو ربات هست یا نه !
راهنما : /helpcontact

راه چهارم 👈 : آیدی‌عددی (id number) اون شخص رو الان وارد ربات کن !
راهنما : /helpid

(در روش دوم و سوم لازمه مخاطبت دسترسی بات‌ها به دیدن حسابش از طریق کانتکت یا فوروارد پیام رو نبسته باشه)

';

$update = DB::table('users')->update(['tel_id' => $fromID], ['check_id' => 1]);

// Send the initial message
if ($update) {
    bot::sendMessage($chatID, $nachricht, $message_id);
}
