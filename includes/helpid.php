<?php

$helfencontact = '<b>
آیدی نامبر یا آیدی عددی تلگرام چیه؟
</b>
هر اکانت تلگرامی یک آیدی عددی مخصوص به خودش داره که قابل تغییر نیست. این آیدی عددی همیشه ثابت می‌مونه و برخلاف یوزرنیم، کاربر نمی‌تونه اون رو تغییر بده. برای دریافت آیدی عددی هر کاربر، می‌تونید از اپلیکیشن‌های تلگرام مثل Telegram Plus یا Plus Messenger استفاده کنید. بعد از نصب و راه‌اندازی، وارد پروفایل کاربر مورد نظرتون بشید و در بخش بیو یا اطلاعات کاربر، آیدی عددی یا User ID نمایش داده میشه.

اگر همچنان موفق نشدید، می‌تونید با ابزارهای خارجی یا ربات‌های مخصوص هم آیدی عددی کاربر رو پیدا کنید.
';

bot::sendMessage($fromID, $helfencontact, $message_id);
