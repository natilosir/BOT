<?php

preg_match('/start=([a-zA-Z0-9_]+)/', $text, $matches);

if (isset($matches[1])) {
   $startValue = $matches[1];
}

$user = DB::table('users')->where('tel_id', $from_id)->first();


if ($user->link_token) {
    $link = $user->link_token;
} else {
    $link = substr(md5(rand(99, 9999)), 2, 10);
    DB::table('users')->update($user->id, [
        'link_token' => $link,
        'created_at' => time(),
    ]);

}


$getChat = http('getMe');
bot::sendMessage($from_id, "لینک ناشناس شما <a 
href='https://t.me/".$getChat->result->username."?start=$link'>$link</a> است.",$message_id);

