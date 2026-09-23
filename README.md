# BOT
Driver-based PHP bot package for Telegram and Bale

```bash
composer require natilosir/BOT
```
```bash
git clone https://github.com/natilosir/BOT/
```

- [ORM](https://github.com/natilosir/orm)
  - select
  - insert
  - table
  - update
  - delete
  - eloquent
  - search

- [Error log](https://github.com/natilosir/BOT/blob/main/error.txt) Advanced error management and storing logs in a separate file for every request to the server.
- [Route Class](https://github.com/natilosir/BOT/blob/main/Router.php)
- [Object Method api telegram](https://github.com/natilosir/BOT/blob/main/core.php)
   - [HTTP request](https://github.com/natilosir/BOT/blob/main/includes/http.php)
   - answerCallbackQuery
   - sendChatAction
   - deleteMessage
   - forwardMessage
   - sendMessage
   - copyMessage
     - inline
     - keyboard
       - editMessageReplyMarkup
       - column
       - row


---

## Bootstrap

The application must be bootstrapped once before using `bot`, `paths()`, models,
routes or other container-backed services. Keep this in the project entry point
(or require a dedicated `bootstrap.php` from the entry point):

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

`Bootstrap` is intentionally platform-agnostic. It only owns application paths,
configuration, the Illuminate container and service registration. Telegram/Bale
selection remains the responsibility of `DriverManager` and the webhook resolver.

The old bootstrap contract is therefore preserved: existing project code using
`$app = new Bootstrap($paths);` continues to work unchanged.

## Bot drivers (Telegram / Bale)

The bot layer is fully driver-based. `BotManager` is platform-agnostic and only delegates calls to the active driver. Telegram and Bale own separate drivers and separate trait trees.

### Directory structure

```text
src/bot/
├── BotManager.php
├── DriverManager.php
├── Contracts/
│   ├── BotDriver.php
│   └── WebhookAwareDriver.php
├── Webhook/
│   ├── WebhookRequest.php
│   └── WebhookDriverResolver.php
└── Drivers/
    ├── AbstractBotDriver.php
    ├── Telegram/
    │   ├── TelegramDriver.php
    │   └── Traits/...
    └── Bale/
        ├── BaleDriver.php
        └── Traits/...
```

### Configuration

Keep the real tokens only under their own drivers. `paths()->config('bot.token')` is a virtual compatibility alias: it reads `bot.default` and returns `bot.drivers.<default>.token`, so the token is never duplicated in configuration.

```php
$bot = [
    'default' => 'bale', // telegram | bale

    'drivers' => [
        'telegram' => [
            'token' => 'YOUR_TELEGRAM_TOKEN',
            'base_url' => 'https://api.telegram.org',
            'webhook' => [
                'url' => 'http://1.bot.borzan.ir/webhook/telegram',
                'secret_token' => 'YOUR_TELEGRAM_WEBHOOK_SECRET',
            ],
        ],

        'bale' => [
            'token' => 'YOUR_BALE_TOKEN',
            'base_url' => 'https://tapi.bale.ai',
            'webhook' => [
                'url' => 'http://1.bot.borzan.ir/webhook/bale',
            ],
        ],
    ],
];


return [
    'bot' => $bot,
];
```

Changing only:

```php
$bot['default'] = 'telegram';
```

makes both generic bot calls and `paths()->config('bot.token')` resolve to Telegram. Setting it to `bale` makes both resolve to Bale.

### Default outgoing driver

```php
bot::sendMessage($chatId, 'سلام');
```

The call is delegated to the driver selected by `bot.default`, unless the current request was recognized as a webhook from another configured driver.

### Complete API coverage

The Telegram driver exposes explicit wrappers for every method documented in Telegram Bot API 10.3, including `getUserProfileAudios`, `setUserEmojiStatus`, Rich Messages, Ephemeral Messages, Guest Mode, Managed Bots, Business, Gifts, Stories and Join Request Query methods. `getUpdates` is also exposed even though webhook mode and long polling are mutually exclusive.

The Bale driver exposes all 50 methods currently documented by Bale, plus the 7 methods supported by Bale Business API through `business*` wrappers.

Each driver also keeps a low-level `api($method, $data)` escape hatch for forward compatibility when a platform adds a method before this package is updated.

### Automatic webhook driver detection

Use a unique webhook endpoint for each platform:

```text
http://1.bot.borzan.ir/webhook/telegram
http://1.bot.borzan.ir/webhook/bale
```

When a request arrives, `DriverManager` resolves the driver before route/controller code runs. Therefore this controller code automatically answers through the same platform that delivered the update:

```php
bot::sendMessage($request->chatID, 'پاسخ');
```

You can inspect the detected source through:

```php
$request->getDriverName(); // telegram | bale
$request->platform;        // telegram | bale
bot::driverName();         // active driver
```

For Telegram, the path is derived from `webhook.url` (or an explicit `webhook.path` override) and `webhook.secret_token`, when configured, must also match. `setWebhook()` automatically sends the secret and incoming requests are validated using the official `X-Telegram-Bot-Api-Secret-Token` header. Bale does not currently document an equivalent secret header, so it is detected by its dedicated webhook URL/path. For extra protection behind your own reverse proxy, Bale may also use the generic `webhook.header` + `webhook.header_value` pair; when configured, URL/path and custom header must all match.

Because Bale and Telegram update payloads can have the same shape, do not point both bots at one indistinguishable URL without a platform-specific path/header. The resolver deliberately throws on ambiguous matches. In a multi-driver installation it also rejects a POST bot update when no driver matches, instead of silently falling back to `bot.default`.

### Runtime override

```php
bot::useDriver('bale');
bot::sendMessage($chatId, 'سلام از بله');

bot::useDriver('telegram');
bot::sendMessage($chatId, 'Hello from Telegram');
```

### Direct driver access

```php
$bale = bot::driver('bale');
$bale->sendMessage($chatId, 'سلام');
$bale->askReview($userId, 2);
$bale->inquireTransaction($transactionId);

$telegram = bot::driver('telegram');
$telegram->sendMessage($chatId, 'Hello');
```

Telegram-only methods are not composed into Bale and Bale-specific methods are not mixed into Telegram.

### Low-level API

```php
bot::driver('bale')->api('METHOD_NAME', [
    'key' => 'value',
]);
```

### Bale Business API

```php
$bale = bot::driver('bale');

$bale->businessSendMessage([
    'chat_id' => $chatId,
    'text' => 'پیام کسب‌وکاری',
]);
```

Official Bale Bot API documentation: `https://docs.bale.ai/`

---

## PhpStorm / IDE navigation

The package ships with `.phpstorm.meta.php` and strongly typed driver accessors.
For exact autocomplete, signatures and Ctrl+Click navigation to the trait that
implements a method, use one of these forms:

```php
bot::telegram()->sendMessage($chatId, 'Telegram');
// Ctrl+Click sendMessage -> Drivers/Telegram/Traits/MessageTrait.php

bot::bale()->sendMessage($chatId, 'Bale');
// Ctrl+Click sendMessage -> Drivers/Bale/Traits/MessageTrait.php

bot::driver('telegram')->sendPhoto(...);
bot::driver('bale')->askReview(...);
```

`bot::driver('telegram')` and `bot::driver('bale')` are mapped to their concrete
return types by PhpStorm metadata. The old dynamic form remains valid:

```php
bot::sendMessage($chatId, 'runtime selected driver');
```

That form is intentionally runtime-dynamic because an incoming webhook can
change the active driver. An IDE cannot know the source of a future HTTP request,
so exact trait navigation is provided by the typed accessors above.

## Borzan webhook endpoints

The default package configuration uses these endpoints:

```text
Telegram: http://1.bot.borzan.ir/webhook/telegram
Bale:     http://1.bot.borzan.ir/webhook/bale
```

Only the full `webhook.url` needs to be configured. The resolver derives the
path from that URL, so there is no duplicated path value that can become stale.
An explicit `webhook.path` is still supported as an override for reverse proxies.

Incoming webhook resolution works in this order:

1. Request must be POST and contain a bot `update_id`.
2. The resolver checks all configured drivers.
3. The configured webhook URL determines the expected host and path.
4. Telegram additionally validates `X-Telegram-Bot-Api-Secret-Token` when a
   `secret_token` is configured.
5. Exactly one driver must match. Zero matches in a multi-driver bot or more
   than one match fails fast; the code never falls back to the wrong token.
6. The detected driver becomes the request context, so ordinary calls such as
   `bot::sendMessage(...)` answer through the same platform.

Calling these methods registers the configured URL automatically:

```php
bot::telegram()->setWebhook();
bot::bale()->setWebhook();
```

Important: Telegram's hosted Bot API requires an **HTTPS** webhook URL. The
HTTP Telegram URL above is kept exactly as requested and is usable with a local
Telegram Bot API server; when using `api.telegram.org`, put TLS in front of
`1.bot.borzan.ir` and change the Telegram webhook URL to `https://...` before
calling `setWebhook()`.
