<?php

$user = DB::table('users')->where('tel_id', $fromID)->first();
$id   = $user->id;
$sex  = $user->sex;

// If the gender is not set, prompt the user to select one
if (empty($sex)) {
    $response = Bot::row([
        Bot::column('من 🙍‍♀ دخترم', 'girl'),
        Bot::column('من 🙎‍♂ پسرم', 'boy'),
    ]);

    $text = '🚹🚺 لطفاً جنسیت خود را تعیین کنید:
<blockquote>🔒 توجه: این گزینه در آینده قابل ویرایش نیست!</blockquote>';
    Bot::keyboard($chatID, $text, $message_id);

    // Stop further processing to wait for user input
    return;
}

// Gender is already set; display search options
$response = Bot::row([
    Bot::column('حتما دختر باشه 🙍‍♀', 'girl_only'),
    Bot::column('حتما پسر باشه 🙎‍♂', 'boy_only'),
])
    ->row([
        Bot::column('🎲 جستجوی شانسی', 'lucky'),
    ])
    ->row([
        Bot::column('انصراف', 'cancel'),
    ]);

$text = 'حله! به کی وصلت کنم؟';
Bot::keyboard($chatID, $text, $message_id);

// Handle callback data
if (! empty($callback_data)) {
    switch ($callback_data) {
        case 'girl_only':
            $text = '🔎 درحال جستجوی یک دختر برای شما ...
▫️ اگر پس از 2 دقیقه به کاربری متصل نشدید مجددا امتحان کنید.';
            break;

        case 'boy_only':

            $user = DB::table('users')->where('tel_id', $fromID)->first();
            $id   = $user->id;
            $sex  = $user->sex;

            // Handle gender selection if not set
            if (empty($sex)) {
                $response = Bot::row([
                    Bot::column('من 🙍‍♀  دخترم', 'boy'),
                    Bot::column('من 🙎‍♂ پسرم', 'girl'),
                ]);

                $text = '🚹🚺 لطفاً جنسیت خود را تعیین کنید:
<blockquote>🔒 توجه: این گزینه در آینده قابل ویرایش نیست!</blockquote>';
                $coin     = $coin - 20; // Adjust coins
                $response = Bot::keyboard($chatID, $text, $message_id);
            } else {
                // Display options for searching
                $response = Bot::row([
                    Bot::column('حتما دختر باشه 🙍‍♀', 'girl_only'),
                    Bot::column('حتما پسر باشه 🙎‍♂', 'boy_only'),
                ])
                    ->row([
                        Bot::column('🎲 جستجوی شانسی', 'lucky'),
                    ])
                    ->row([
                        Bot::column('انصراف', 'cancle'),
                    ]);

                $text     = 'حله! به کی وصلت کنم؟';
                $response = Bot::keyboard($chatID, $text, $message_id);
            }

            // Define acceptable callback names
            //$valid_callbacks = ['lucky', 'girl_only', 'boy_only'];

            // Check if the user's callback matches any valid option
            //if (in_array($callback_data, $valid_callbacks)) {
            //    $response = Bot::row([
            //        Bot::column('لغو جستوجو', 'cancle'),
            //    ]);
            //
            //    $text = '🔎 درحال جستجوی مخاطب ناشناس شما ...
            //
            //▫️اگر پس از 2 دقیقه به کاربری متصل نشدید مجددا امتحان کنید .';
            //
            //    $response = Bot::keyboard($chatID, $text, $message_id);
            //}

            // Handle match preferences
            if (in_array($callback_data, ['lucky', 'girl_only', 'boy_only'])) {
                $match = findMatch($fromID, $callback_data);

                if ($match) {
                    // Notify both users about the connection
                    Bot::sendMessage($fromID, 'شما به یک کاربر وصل شدی');
                    Bot::sendMessage($match->telegram_id, 'شما به یک کاربر وصل شدی');
                    // Handle message forwarding in a loop (e.g., Webhook listener)
                } else {
                    Bot::sendMessage($chatID, 'Searching for a match... Please wait.');
                }
            }
            function findMatch($user_id, $preference)
            {
                $user = DB::table('users')->where('id', $user_id)->first();

                $query = DB::table('users')->where('is_online', true)->where('id', '!=', $user_id);

                if ($preference === 'connect_male') {
                    $query->where('sex', 'male');
                } elseif ($preference === 'connect_female') {
                    $query->where('sex', 'female');
                }

                return $query->inRandomOrder()->first();
            }
    }
}
