<?php

// تجزیه callbackData به اجزا
$dataParts = explode('_', $callbackData);

// اجزای callback_data
$action                   = isset($dataParts[0]) ? $dataParts[0] : null; // قسمت اول (نوع اقدام: reply یا block)
$receiver                 = isset($dataParts[1]) ? $dataParts[1] : null; // قسمت دوم (گیرنده)
$sender                   = isset($dataParts[2]) ? $dataParts[2] : null; // قسمت سوم (فرستنده)
$message_id_from_callback = isset($dataParts[3]) ? $dataParts[3] : null; // قسمت چهارم (شناسه پیام)

// پردازش عملیات بر اساس نوع action
// تعریف اکشن reply
Route::add('reply', function () use ($receiver, $sender, $chatID, $message_id) {
    $the_user    = DB::table('users')->where('tel_id', $receiver)->first();
    $second_user = DB::table('users')->where('tel_id', $sender)->first();

    DB::table('users')->update($the_user->id, ['customer' => $second_user->id]);

    Bot::row([
        Bot::column('انصراف'),
    ]);

    Bot::keyboard($chatID, '☝️ در حال پاسخ دادن به فرستنده این پیام هستی ... ؛ منتظریم بفرستی :)', $message_id);
})
    ->add('block', function () use ($receiver, $sender, $message_id_from_callback, $message_id, $chatID, $query_id) {
        // Retrieve receiver data

        $find = DB::Table('blocks')
            ->where('subject', $receiver)
            ->where('object', $sender)
            ->first();
            
if($find){
        Bot::alert($query_id, 'already blocked');
}
        else{
            $blockedUser = [

            'subject'  => $receiver,
            'object'  => $sender];
        DB::Table('blocks')
            ->insert($blockedUser);

        


        Bot::row([
            Bot::column('🔓 آزادسازی', 'unblock_'.$receiver.'_'.$sender.'_'.$message_id_from_callback),
            Bot::column('✍ پاسخ', 'reply_'.$receiver.'_'.$sender.'_'.$message_id_from_callback),
        ])
            ->row([
                Bot::column('🚫 گزارش کاربر', 'report_'.$receiver.'_'.$sender.'_'.$message_id_from_callback),
            ]);

        Bot::alert($query_id, '🚫 بلاک شد !');

        $tel_response = Bot::inline($chatID, null, $message_id, 'edit');
        if (isset($tel_response->ok)) {
            // اقدام مورد نیاز در صورت موفقیت
        }}
    })
    ->add('unblock', function () use ($receiver, $sender, $message_id, $chatID) {
            $blockedUser = [
            'subject'  => $receiver,
            'object'  => $sender];
            $users = DB::Table('blocks')
    ->delete($blockedUser);

    if($users){
        Bot::sendMessage($chatID, 'unfuckin block', $message_id);
    }});

// تنظیم مقدار پیش‌فرض (در صورت نیاز)
Route::def('default_action.php');
// فرض کنید $action ورودی اکشن است
Route::handle($action);
