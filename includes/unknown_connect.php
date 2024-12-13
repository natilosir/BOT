<?php

$the_user = $user->where('tel_id', $fromID)->first();
if (! $the_user) {
    Bot::sendMessage($chatID, 'Error: User not found.');
    exit;
}
$id  = $the_user->id;
$sex = $the_user->sex;

// Handle gender selection if not set
if (empty($sex)) {
    $response = Bot::row([
        Bot::column('من 🙍‍♀ دخترم', 'girl'),
        Bot::column('من 🙎‍♂ پسرم', 'boy'),
    ]);

    $text = '🚹🚺 لطفاً جنسیت خود را تعیین کنید:
<blockquote>🔒 توجه: این گزینه در آینده قابل ویرایش نیست!</blockquote>';
    $coin     = $coin - 20; // Adjust coins
    $response = Bot::keyboard($chatID, $text, $message_id);
} else {
    $map = [
        '🎲 جستجوی شانسی'     => 1,
        'حتما دختر باشه 🙍‍♀' => 2,
        'حتما پسر باشه 🙎‍♂'  => 3,
    ];

    // Normalize $text to prevent formatting issues
    $normalized_text = trim($text);

    if (array_key_exists($normalized_text, $map)) {
        $upunk = $user->update(
            ['id' => $id],
            ['unk_connect' => $map[$normalized_text], 'updated_at' => time()]
        );
    }

    if ($upunk) {
        $response = Bot::row([
            Bot::column('لغو جستوجو', 'cancel'),
        ]);

        $text = '🔎 درحال جستجوی مخاطب ناشناس شما ...

▫️اگر پس از 2 دقیقه به کاربری متصل نشدید مجددا امتحان کنید .';

        $response = Bot::keyboard($chatID, $text, $message_id);

        // Match user
        $query = $user->where('unk_connect', '!=', 0)->where('id', '!=', $id);

        // Define matching criteria
        if ($text == 'حتما دختر باشه 🙍‍♀') {
            $query = $query->where('sex', 'F')->where(function ($query) {
                $query->where('unk_connect', 3) // User requesting boy
                    ->Where('unk_connect', 1); // Lucky search
            });
        } elseif ($text == 'حتما پسر باشه 🙎‍♂') {
            $query = $query->where('sex', 'M')->where(function ($query) {
                $query->where('unk_connect', 2) // User requesting girl
                    ->Where('unk_connect', 1); // Lucky search
            });
        }

        $query = $query->orderBy('id', 'ASC');
        $match = $query->first();

        if ($match) {
            $chatID_1 = $the_user->tel_id;
            $chatID_2 = $match->tel_id;

            $text = '🔗 شما به یک مخاطب ناشناس وصل شدید!';
            Bot::sendMessage($chatID_1, $text);
            Bot::sendMessage($chatID_2, $text);

            // Forward messages between users
            while (true) {
                $message1 = Bot::receiveMessage($chatID_1);
                $message2 = Bot::receiveMessage($chatID_2);

                if ($message1) {
                    Bot::sendMessage($chatID_2, $message1);
                }
                if ($message2) {
                    Bot::sendMessage($chatID_1, $message2);
                }
            }
        } else {
            sleep(1);
            $text = 'متاسفانه کاربری یافت نشد، لطفا مجددا تلاش کنید.';
            Bot::sendMessage($chatID, $text);
        }
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
                Bot::column('انصراف', 'cancel'),
            ]);

        $text     = 'حله! به کی وصلت کنم؟';
        $response = Bot::keyboard($chatID, $text, $message_id);
    }
}
