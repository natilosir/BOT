<?php

// دریافت اطلاعات کاربر
$the_user = $user->where('tel_id', $fromID)->first();
if (! $the_user) {
    Bot::sendMessage($chatID, 'Error: User not found.');
    exit;
}

if ($the_user->check_id == 1) {
    $forward        = $message->forward_from;
    $forward_origin = $message->forward_origin;
    $hidden_user    = $message->forward_origin->type;

    // Step 1: Use explode to split on '@'
    $parts = explode('@', $text);

    // Step 2: Determine the username
    if (count($parts) == 2) {
        // If '@' exists, take the second part as the username
        $second_user = DB::table('users')->where('username', $parts[1])->first();

        $sent_by_id = DB::table('users')->update($the_user->id, ['customer' => $second_user->id]);
    }
}

$customer = $the_user->customer;
$state    = $the_user->state;

switch (true) {
    case $state:
        $start_if_in_includes_resetname_php_file = 1;
        require_once 'includes/resetname.php';

        break;

    case $customer:
        $second_user = DB::table('users')->where('id', $customer)->first();
        if ($second_user->id) {
            Bot::sendMessage($second_user->tel_id, '📬 یه پیام ناشناس جدید داری !

جهت دریافت کلیک کنید 👈 /newmsg');

            $msg = [
                'sender'     => $fromID,
                'receiver'   => $second_user->tel_id,
                'message_id' => $message_id,
            ];
            $insertok = DB::table('msg')->insert($msg);

            if ($insertok) {
                foreach ([$second_user, $the_user] as $user) {
                    DB::table('users')->update(['id' => $user->id], ['customer' => 0]);
                    $default_check = 2;
                    include_once 'includes/start.php';
                }
            }
        }
        break;

    case $forward_origin:

        $second_user = DB::table('users')->where('tel_id', $forward->id)->first();

        $find = DB::Table('blocks')
            ->where('subject', $forward->id)
            ->where('object', $fromID)
            ->first();

        $otherfind = DB::Table('blocks')
            ->where('subject', $fromID)
            ->where('object', $forward->id)
            ->first();

        if ($hidden_user == 'hidden_user') {
            Bot::sendMessage($chatID, 'پیام سیستم:

متاسفانه مخاطبت دسترسی به فوروارد پیام را بسته است، به همین دلیل نمی‌توانیم او را شناسایی کنیم. 🚫', $message_id);
        } elseif ($find) {
            Bot::sendMessage($chatID, '
پیام سیستم:

مخاطبی که می‌خوای بهش پیام بدی، قبلاً تو رو بلاک کرده. ❌

متاسفانه امکان ارسال پیام ناشناس بهش وجود نداره.

چه کاری می‌تونم برات انجام بدم؟
', $message_id);
        } elseif ($otherfind) {
            Bot::sendMessage($chatID, 'مگه بلاکش نکردی', $message_id);
        } else {
            if ($second_user->id) {
                DB::table('users')->update($the_user->id, ['customer' => $second_user->id]);
                Bot::row([
                    Bot::column('انصراف'),
                ]);
                Bot::keyboard($chatID, 'شما در حال پیام دادن به '.$second_user->first_name.' هستید', $message_id);
            } else {
                Bot::sendMessage($chatID, 'کاربری که برام فرستادی توی ربات استارت نکرده', $message_id);
            }
        }
        break;

    case $sent_by_id:

        Bot::keyboard($chatID, 'شما در حال پیام دادن به '.$second_user->first_name.' هستید', $message_id);

        break;

    default:
        $default_check = 1;
        include_once 'includes/start.php';
        break;
}
