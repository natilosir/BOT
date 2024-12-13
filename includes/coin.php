<?php

function checkButtonPress($user_id, $message_date, $chatID)
{
    // Fetch the user data
    $user = DB::table('users')->where('tel_id', $user_id)->first();

    if (! $user) {
        Bot::sendMessage($chatID, 'User not found.');
        error_log('User not found: '.$user_id);

        return;
    }

    // Get last button press timestamp from the user
    $last_press_timestamp = $user->created_at ?: 0;

    // If the user pressed the button today
    if (date('Y-m-d', $last_press_timestamp) === date('Y-m-d', $message_date)) {
        // Calculate remaining time (24-hour window)
        $remaining    = (24 * 3600) - ($message_date - $last_press_timestamp); // In seconds
        $hours_left   = floor($remaining / 3600);
        $minutes_left = floor(($remaining % 3600) / 60);
        $seconds_left = $remaining % 60;

        Bot::sendMessage($chatID, "
 ❕شما به تازگی سکه رایگان دریافت کرده اید .
        
⏰ {$hours_left} ساعت و {$minutes_left} دقیقه و {$seconds_left} ثانیه دیگر می‌توانید سکه رایگان دریافت کنید.
 
 
        ");
    } else {
        // Update last button press time
        $new_coin_count = $user->coin + 2; // Increment coins by 2
        DB::table('users')->update($user->id, [
            'created_at' => $message_date, // Update last press timestamp
            'coin'       => $new_coin_count, // Update coin count
        ]);

        Bot::sendMessage($chatID, '
کاربر عزیز ، 2 سکه رایگان امروز رو دریافت کردی !😀


 یادت نره ، هر روز  با لمس دستور 👈 /coin 👉 میتونی  2 سکه رایگان دریافت کنی !🎊
 
 ');
    }
}

$message->date = strtotime('now');

checkButtonPress($fromID, $message->date, $chatID);
