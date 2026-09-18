# Telegram Bot SDK برای PHP

[![Packagist](https://img.shields.io/packagist/v/natilosir/telegram-bot-sdk.svg)](https://packagist.org/packages/natilosir/telegram-bot-sdk)
[![License](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)

یک SDK سبک برای ساخت ربات تلگرام با PHP و ساختاری الهام‌گرفته از Laravel؛ شامل مسیریابی، state پایدار برای مکالمه،
مدل‌های Eloquent، HTTP Client مبتنی بر Illuminate، container و dependency injection، مسیرهای قابل تنظیم پروژه و لاگر
HTML برای دیباگ.

**زبان:** [English](README.md) · فارسی

> این مستندات معماری و رفتار عمومی نسخه فعلی SDK و سورس جدید `natilosir/bot` مورد استفاده پروژه را توضیح می‌دهد.

## فهرست مطالب

- [قابلیت‌ها](#قابلیت‌ها)
- [پیش‌نیازها](#پیشنیازها)
- [نصب](#نصب)
- [ساختار پروژه](#ساختار-پروژه)
- [Bootstrap](#bootstrap)
- [تنظیمات](#تنظیمات)
- [Helperها و Container](#helperها-و-container)
- [مسیریابی](#مسیریابی)
- [مدیریت State](#مدیریت-state)
- [Request](#request)
- [ارسال درخواست از برنامه دیگر به Webhook ربات](#ارسال-درخواست-از-برنامه-دیگر-به-webhook-ربات)
- [HTTP Client](#http-client)
- [HTTP Response](#http-response)
- [دیتابیس و مدل‌های Eloquent](#دیتابیس-و-مدلهای-eloquent)
- [Logging و Debugging](#logging-و-debugging)
- [Helperهای Telegram Bot API](#helperهای-telegram-bot-api)
- [Keyboard Builder](#keyboard-builder)
- [ارسال و ویرایش عکس](#ارسال-و-ویرایش-عکس)
- [نکات مهاجرت از README قدیمی](#نکات-مهاجرت-از-readme-قدیمی)
- [مجوز](#مجوز)

## قابلیت‌ها

- Bootstrap برنامه با ساختاری شبیه Laravel.
- استفاده از Illuminate Service Container از طریق `Bootstrap`، `Container` و helper سراسری `app()`.
- امکان تنظیم مسیر app، routes، config، storage و log.
- پشتیبانی از config به‌صورت یک فایل PHP یا یک پوشه شامل چند فایل config.
- مسیریابی پیام‌ها و callbackها به controller، callable یا کلاس invokable.
- dispatch خودکار routeها در پایان request پس از ثبت routeها.
- مدیریت state پایدار مکالمه با استفاده از `app\Models\User`.
- تجزیه Webhook تلگرام برای message، callback، inline query، پرداخت، poll و تغییرات chat member.
- پشتیبانی از درخواست‌های برنامه‌ای با فیلد اختصاصی `route`.
- پشتیبانی از multipart/form-data و فایل‌های آپلودشده.
- HTTP Client مبتنی بر Illuminate با متدهای fluent و Response wrapper اختصاصی.
- ORM مبتنی بر `illuminate/database` و Eloquent.
- لاگ HTML پیشرفته همراه با محل فراخوانی، backtrace، exception handling و ثبت fatal error.
- helperهای تلگرام برای message، photo، callback، forward، copy، delete، chat action، inline keyboard و reply keyboard.
- مخفی‌سازی خودکار Bot Token از متن exceptionهایی که در HTTP helper سطح پایین تلگرام ساخته می‌شوند.

## پیش‌نیازها

سورس فعلی از قابلیت‌هایی مانند return type نوع `never` استفاده می‌کند؛ بنابراین پیشنهاد و نیاز عملی این نسخه:

- **PHP 8.1+**
- **Composer**
- افزونه **PDO**
- افزونه **cURL**
- در صورت استفاده از Eloquent و State، یک دیتابیس سازگار با PDO؛ تنظیم پیش‌فرض MySQL است.
- افزونه `mbstring` توصیه می‌شود، چون caption عکس در کد از `mb_convert_encoding()` استفاده می‌کند.

وابستگی‌های PHP لازم از طریق Composer نصب می‌شوند؛ از جمله `natilosir/bot`، پکیج‌های Illuminate و Verta که در ساختار
فعلی پروژه استفاده می‌شوند.

## معماری پکیج

این repository پکیج application/scaffold با نام `natilosir/telegram-bot-sdk` است. هسته اصلی Bot از طریق dependency
کامپوزر با نام `natilosir/bot` تأمین می‌شود؛ همان هسته‌ای که سورس Bot ارائه‌شده مربوط به آن است. مخزن Telegram-Bot-SDK
ساختار برنامه شامل `app/`، `Router/`، `index.php`، config و integration مربوط به Verta را در کنار آن هسته فراهم می‌کند.

## نصب

پکیج SDK را از Packagist نصب کنید:

```bash
composer require natilosir/telegram-bot-sdk
```

سپس installer یک‌باره را اجرا کنید:

```bash
php vendor/natilosir/bot/install.php
```

Installer فایل‌های scaffold پروژه را به ریشه پروژه منتقل/منتشر می‌کند و اطلاعات زیر را از شما می‌گیرد:

- Telegram Bot API Token
- آدرس host دیتابیس
- نام کاربری دیتابیس
- رمز عبور دیتابیس
- نام دیتابیس

سپس فایل `config.php` را در ریشه پروژه ایجاد می‌کند.

> **رفتار Installer:** اگر از قبل در ریشه پروژه فایل `config.php` وجود داشته باشد، installer بدون انجام تنظیم مجدد خارج
> می‌شود. فقط زمانی فایل را حذف یا backup کنید که واقعاً قصد دارید installer را دوباره اجرا کنید.

## ساختار پروژه

ساختار معمول پروژه پس از نصب:

```text
project/
├── app/
│   ├── Controllers/
│   ├── Models/
│   └── State/
├── Router/
│   ├── route.php
│   └── state.php
├── storage/
├── config.php
├── index.php
├── log.html
└── vendor/
```

هسته پکیج توسط Composer در مسیر `vendor/natilosir/bot` قرار می‌گیرد.

## Bootstrap

فایل `index.php` نقطه ورود اصلی webhook و برنامه است. ابتدا autoload مربوط به Composer را لود کرده و سپس container اصلی
SDK را بسازید:

```php
<?php

use natilosir\bot\Bootstrap;

require __DIR__ . '/vendor/autoload.php';

$paths = [
    'base_path'    => __DIR__,
    'app_path'     => __DIR__ . '/app',
    'route_path'   => __DIR__ . '/Router',
    'config_path'  => __DIR__ . '/config.php',
    'storage_path' => __DIR__ . '/storage',
    'log_path'     => __DIR__ . '/log.html',
];

$app = new Bootstrap($paths);
```

### Bootstrap چه کاری انجام می‌دهد؟

هنگام ساخت `Bootstrap` این مراحل انجام می‌شود:

1. instance اصلی Illuminate Container ثبت می‌شود.
2. مسیرهای پروژه resolve و داخل container ثبت می‌شوند.
3. خود Bootstrap با کلیدهای `app`، `bootstrap`، `Bootstrap::class` و `Container::class` bind می‌شود.
4. فایل config یا پوشه config مشخص‌شده لود می‌شود.
5. در صورت وجود `timezone`، timezone PHP تنظیم می‌شود.
6. فایل `Router/route.php` لود می‌شود.
7. HTML logger راه‌اندازی می‌شود.

### مسیرهای پیش‌فرض

کلیدهای زیر قابل تنظیم هستند:

| کلید           | مقدار نسبی پیش‌فرض    |
|----------------|----------------------|
| `base_path`    | مسیر پایه پکیج/پروژه |
| `app_path`     | `app`                |
| `route_path`   | `Router`             |
| `config_path`  | `config.php`         |
| `storage_path` | `storage`            |
| `log_path`     | `log.html`           |

مسیرهای نسبی بر اساس `base_path` ساخته می‌شوند و مسیرهای absolute بدون تغییر استفاده می‌شوند.

### API مربوط به مسیرها در Bootstrap

```php
$app->basePath();
$app->appPath('Controllers');
$app->routePath('route.php');
$app->configPath();
$app->storagePath('cache');
$app->logPath();
$app->path('app_path', 'Models/User.php');
$app->paths();
```

می‌توانید در runtime نیز مسیرها را تغییر دهید:

```php
$app->setPath('storage_path', __DIR__ . '/var/storage');
$app->setBasePath(__DIR__);
```

برای دریافت Bootstrap فعال:

```php
$app = Bootstrap::getInstance();
```

## تنظیمات

نمونه یک فایل config کامل:

```php
<?php

return [
    'timezone' => 'Asia/Tehran',
    'locale'   => 'fa',
    'calendar' => 'jalali',

    'bot' => [
        'token' => 'YOUR_TELEGRAM_BOT_TOKEN',
    ],

    'database' => [
        'driver'    => 'mysql',
        'host'      => 'localhost',
        'port'      => 3306,
        'database'  => 'your_database',
        'user'      => 'root',
        'password'  => '',
        'charset'   => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'prefix'    => '',
        'strict'    => true,
    ],
];
```

خواندن config با dot notation:

```php
$token = paths()->config('bot.token');
$host  = paths()->config('database.host', 'localhost');
```

یا از طریق Bootstrap:

```php
$timezone = $app->config('timezone');
$all      = $app->config();
```

### استفاده از پوشه config

`config_path` می‌تواند به‌جای یک فایل، مسیر یک پوشه باشد. تمام فایل‌های `*.php` داخل آن پوشه با نام فایل به‌عنوان key
لود می‌شوند.

مثال:

```text
config/
├── app.php
├── bot.php
└── database.php
```

اگر `database.php` آرایه تنظیمات دیتابیس را برگرداند:

```php
paths()->config('database.host');
```

## Helperها و Container

Composer فایل `src/helpers.php` را به‌صورت سراسری autoload می‌کند.

### `app()`

دریافت container فعلی:

```php
$container = app();
```

Resolve کردن یک کلاس یا binding:

```php
$service = app(MyService::class);
```

ارسال پارامتر هنگام resolve:

```php
$service = app(MyService::class, ['name' => 'example']);
```

### `paths()`

`paths()` یک object برمی‌گرداند که aliasهای مسیر را به‌صورت property یا method در اختیار می‌گذارد:

```php
paths()->base;
paths()->app;
paths()->route;
paths()->router;   // alias برای route
paths()->config;
paths()->storage;
paths()->log;
paths()->logs;     // alias برای log
```

اضافه کردن یک مسیر نسبی:

```php
paths()->app('Controllers/StartController.php');
paths()->route('state.php');
paths()->storage('cache/data.json');
```

خواندن config:

```php
paths()->config('bot.token');
```

### Helperهای Debug

```php
lg($value);       // ثبت با سطح DEBUG
lg($a, $b, $c);  // هر مقدار جداگانه لاگ می‌شود
dad($value);      // alias برای DEBUG log
dd($value);       // لاگ و سپس توقف اجرای برنامه
```

> خود PHP از قبل یک تابع ریاضی داخلی با نام `log()` دارد. به همین دلیل SDK در runtime نمی‌تواند با اطمینان یک helper
> سراسری logger با نام `log()` ثبت کند. برای لاگ از `lg()`، `dad()` یا `Log::debug()` استفاده کنید.

## مسیریابی

Routeها معمولاً در `Router/route.php` تعریف می‌شوند:

```php
<?php

use app\Controllers\StartController;
use natilosir\bot\Route;

Route::add(
    ['/start', '🏠 بازگشت', 'انصراف'],
    [StartController::class, 'hello']
);
```

پس از ثبت حداقل یک route یا default route، سیستم dispatch خودکار را برای پایان request ثبت می‌کند. در entry point معمول
پروژه نیازی نیست `Route::dispatch()` را دستی اجرا کنید.

### نمونه Controller

```php
<?php

namespace app\Controllers;

use natilosir\bot\bot as Bot;
use natilosir\bot\Request;

class StartController
{
    public function hello(Request $request)
    {
        return Bot::sendMessage(
            $request->chatID,
            'Welcome to the bot!'
        );
    }
}
```

### `Route::add($uri, $action)`

پارامتر `$uri` می‌تواند string یا آرایه‌ای از stringها باشد:

```php
Route::add('/start', [StartController::class, 'hello']);
Route::add(['/start', 'Home'], [StartController::class, 'hello']);
```

انواع action پشتیبانی‌شده:

```php
// Controller + method
Route::add('/start', [StartController::class, 'hello']);

// Callable
Route::add('/ping', function (Request $request) {
    return 'pong';
});

// Invokable class
Route::add('/help', HelpController::class);
```

اگر action به‌صورت class string باشد، Router متد `__invoke()` را اجرا می‌کند.

### نرمال‌سازی ورودی

کلید route پیش از match شدن نرمال می‌شود:

- فاصله ابتدا و انتهای متن حذف می‌شود.
- فاصله‌های تکراری به یک فاصله تبدیل می‌شوند.
- `ي` عربی به `ی` فارسی تبدیل می‌شود.
- `ك` عربی به `ک` فارسی تبدیل می‌شود.

این رفتار مخصوصاً برای ورودی‌های فارسی و دکمه‌های تلگرام مفید است.

### Route پیش‌فرض

```php
Route::def([FallbackController::class, 'handle']);
```

اگر route ثبت‌شده‌ای match نشود و state فعالی نیز request را handle نکند، default route اجرا می‌شود.

### اتصال Route به State

می‌توانید هنگام match شدن یک route، state ذخیره کنید:

```php
Route::add('/phone', [ProfileController::class, 'askPhone'])
    ->state('phoneNumber');
```

پیش از اجرای controller، `State::set('phoneNumber')` فراخوانی می‌شود.

### پاسخ JSON

برای endpointهایی که از طریق Router توسط یک برنامه دیگر فراخوانی می‌شوند:

```php
Route::response([
    'message' => 'ok',
], 200);
```

خروجی:

```json
{
  "status":200,
  "data":{
	"message":"ok"
  }
}
```

`Route::response()` status کد HTTP را تنظیم می‌کند، Content-Type را JSON می‌گذارد، پاسخ را چاپ می‌کند و execution را
متوقف می‌کند.

## مخزن و پکیج

- GitHub: <https://github.com/natilosir/Telegram-Bot-SDK>
- Packagist: <https://packagist.org/packages/natilosir/telegram-bot-sdk>

## مجوز

این پروژه تحت [MIT License](LICENSE) منتشر شده است.
