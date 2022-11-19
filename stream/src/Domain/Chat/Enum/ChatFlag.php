<?php

declare(strict_types=1);

namespace App\Domain\Chat\Enum;

class ChatFlag
{
    public const HELP = '!h';
    public const REGISTER = '!u';
    public const CHAT = '!c';
    public const MESSAGE = '!m';
    public const MESSAGES = '!g';
    public const LEAVE = '!l';
    public const CHAT_EXIT = '!e';

    public const FLAGS_DESCRIPTION = [
        self::HELP => 'write help',
        self::REGISTER => 'register user',
        self::CHAT => 'go to chat',
        self::MESSAGE => 'write message',
        self::MESSAGES => 'get messages',
        self::LEAVE => 'leave from chat',
        self::CHAT_EXIT => 'exit',
    ];
}
