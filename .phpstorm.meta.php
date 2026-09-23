<?php

namespace PHPSTORM_META {

    override(\natilosir\bot\Bot::driver(0), map([
        'telegram' => \natilosir\bot\Bot\Drivers\Telegram\TelegramDriver::class,
        'bale' => \natilosir\bot\Bot\Drivers\Bale\BaleDriver::class,
    ]));


    override(\natilosir\bot\Bot\Manager\BotManager::driver(0), map([
        'telegram' => \natilosir\bot\Bot\Drivers\Telegram\TelegramDriver::class,
        'bale' => \natilosir\bot\Bot\Drivers\Bale\BaleDriver::class,
    ]));

    override(\natilosir\bot\Bot\DriverManager::driver(0), map([
        'telegram' => \natilosir\bot\Bot\Drivers\Telegram\TelegramDriver::class,
        'bale' => \natilosir\bot\Bot\Drivers\Bale\BaleDriver::class,
    ]));
}
