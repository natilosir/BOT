<?php

namespace natilosir\bot\Bot\Contracts;

interface BotDriver {
    public function name(): string;

    public function token(): string;

    public function baseUrl(): string;

    public function supports( string $method ): bool;

    public function api( string $method, array $data = [], string $httpMethod = 'POST' ): mixed;

    public function fileUrl( string $filePath ): string;

    public function beginCapture(): void;

    public function endCapture(): ?array;
}
