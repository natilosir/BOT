<?php

if(empty($start_if_in_includes_resetname_php_file)){
$the_user =  $user->where('tel_id', $fromID)->first(); 

        $set_update_db=$user->update(['tel_id' => $fromID], ['state' => 1]);

        if($set_update_db){
        Bot::sendMessage($chatID, "
الان زمانی که کسی بخواد از طریق لینک ناشناست بهت پیام بده با اسم 👈 ".$the_user->first_name." 👉 نمایش داده میشی!


اگه میخوای اسم نمایشی خودتو تغییر بدی ، لطفا اسم دلخواهت رو همینجا ارسال کن 👇
");
}
}


if($start_if_in_includes_resetname_php_file){

            if (!empty($text)) {
                // Update the user's name and clear the state
                $ok_update_db=$user->update(['tel_id' => $fromID], [
                        'first_name' => $text,
                        'state'      => null,
                    ]);

    if ( $ok_update_db ) {

                // Acknowledge the update
                Bot::sendMessage($chatID, "
حله!

از این به بعد از 👈 ".$text." 👉 به عنوان اسم نمایشیت در این برنامه استفاده میشه!

هر موقع خواستی First Name تلگرامت به عنوان اسم نمایشی باشه ، دستور /reset_name رو لمس کن.

چه کاری برات انجام بدم ؟
" );}
            } else {
                // Prompt for valid input
                Bot::sendMessage($chatID, "Please provide a valid name.");
            }
        
        }