<?php

const HELP = '!h';
const REGISTER = '!u';
const CHAT = '!c';
const MESSAGE = '!m';
const MESSAGES = '!g';
const LEAVE = '!l';
const CHAT_EXIT = '!e';

while (true) {
//    stream_filter_register('trimmer', 'ClearMessageFilter');
    $flags = [
        HELP => 'write help',
        REGISTER => 'register user',
        CHAT => 'go to chat',
        MESSAGE => 'write message',
        MESSAGES => 'get messages',
        LEAVE => 'leave from chat',
        CHAT_EXIT => 'exit',
    ];

    $socket = stream_socket_server('tcp://0.0.0.0:8080', $errCode, $errMsg);
    if (!$socket) {
        echo "[ERROR $errCode] $errMsg";
        return;
    }

    while ($conn = stream_socket_accept($socket, -1)) {
//        stream_filter_append($conn, 'trimmer');

        writeHelp($conn, $flags);
        $command = getCommand($conn);

        $currUser = null;
        $currChat = null;

        while ($command['flag'] !== CHAT_EXIT) {
            switch ($command['flag']) {
                case HELP:
                {
                    writeHelp($conn, $flags);
                    break;
                }
                case MESSAGE:
                {
                    if (!$currUser) {
                        fwrite($conn, 'Register pls!' . PHP_EOL);
                        break;
                    }
                    if (!$currChat) {
                        fwrite($conn, 'Choose chat!' . PHP_EOL);
                        break;
                    }

                    if (addMessage($currUser, $currChat, $command['message'])) {
                        fwrite($conn, 'Message was written' . PHP_EOL);
                    } else {
                        fwrite($conn, "Message wasn't written" . PHP_EOL);
                    }
                    break;
                }
                case REGISTER:
                {
                    $user = trim($command['message']);
                    if (empty($user)) {
                        fwrite($conn, "Enter username by \r \<username\>" . PHP_EOL);
                        break;
                    }
                    if (registerUser($user)) {
                        fwrite($conn, 'User was registered' . PHP_EOL);
                    } else {
                        fwrite($conn, "User init" . PHP_EOL);
                    }
                    $currUser = $user;
                    break;
                }
                case LEAVE:
                {
                    fwrite($conn, 'U leave from chat - ' . $currChat . PHP_EOL);
                    $currChat = null;
                    break;
                }
                case CHAT_EXIT:
                {
                    fwrite($conn, 'Bye bye ...' . PHP_EOL);
                    fclose($conn);
                }
                case CHAT:
                {
                    if (!$currUser) {
                        fwrite($conn, 'Register pls!' . PHP_EOL);
                        break;
                    }

                    $userTo = trim($command['message']);
                    if (empty($userTo)) {
                        fwrite($conn, "Choose user by \c \<username\>" . PHP_EOL);
                        break;
                    }
                    if (existsUser($userTo)) {
                        $currChat = $userTo;
                        fwrite($conn, 'Start chat with user - ' . $userTo . PHP_EOL);
                    } else {
                        fwrite($conn, "User doesn't exists" . PHP_EOL);
                    }
                    break;
                }
                case MESSAGES:
                {
                    if (!$currUser) {
                        fwrite($conn, 'Register pls!' . PHP_EOL);
                        break;
                    }
                    if (!$currChat) {
                        fwrite($conn, 'Choose chat!' . PHP_EOL);
                        break;
                    }

                    writeMessages($conn, $currUser, $currChat);
                    break;
                }
            }

            $command = getCommand($conn);
        }

        fwrite($conn, 'Bye bye ...' . PHP_EOL);
        fclose($conn);
    }

    fclose($socket);
}


function addMessage(string $from, string $to, string $message): bool
{
    if (file_exists('messages.json')) {
        $messagesContent = file_get_contents('messages.json');
        if (!$messagesContent) {
            return false;
        }

        $messages = json_decode($messagesContent, true);
        $chat = getOrCreateChatId($from, $to);
        $messages[$chat][] = [
            't' => time(),
            'author' => $from,
            'msg' => $message,
        ];

        return file_put_contents('messages.json', json_encode($messages));
    }

    $messages = [
        getOrCreateChatId($from, $to) => [
            [
                't' => time(),
                'author' => $from,
                'msg' => $message,
            ],
        ],
    ];

    return file_put_contents('messages.json', json_encode($messages));
}

function getOrCreateChatId(string $from, string $to)
{
    if (file_exists('chats.json')) {
        $chatsContent = file_get_contents('chats.json');
        $chats = json_decode($chatsContent, true);
        $chatsCount = $chats['count'];
        if (!isset($chats[sha1($from . $to)], $chats[sha1($to . $from)])) {
            $chatId = $chatsCount + 1;
            $chats[sha1($from . $to)] = $chatId;
            $chats[sha1($to . $from)] = $chatId;
            $chats['count'] = $chatId;

            file_put_contents('chats.json', json_encode($chats));

            return $chatId;
        }

        return $chats[sha1($from . $to)];
    }

    $chats = [
        'count' => 1,
        sha1($from . $to) => 1,
        sha1($to . $from) => 1,
    ];

    file_put_contents('chats.json', json_encode($chats));

    return 1;
}

function writeHelp($conn, array $commands): void
{
    $message = 'Supported command:' . PHP_EOL;
    foreach ($commands as $flag => $command) {
        $message .= $flag . ' - ' . $command . PHP_EOL;
    }
    fwrite($conn, $message);
}

function getCommand($conn): array
{
    $message = fread($conn, 1024);
    $success = preg_match('/^(?<flag>\![hucmgle])(?<message>.*)/', $message, $command);
    while (!$success) {
        fwrite($conn, '[ERROR] unsupported message' . PHP_EOL);
        $message = fread($conn, 1024);
        $success = preg_match('/^(?<flag>\![hucmgle])(?<message>.*)/', $message, $command);
    }

    return $command;
}

function registerUser(string $user): bool
{
    if (file_exists('users.json')) {
        $usersContent = file_get_contents('users.json');
        $users = json_decode($usersContent, true);
        if (isset($users[$user])) {
            return false;
        }

        $users[$user] = 1;
        return file_put_contents('users.json', json_encode($users));
    }

    $users = [
        $user => 1,
    ];

    return file_put_contents('users.json', json_encode($users));
}

function existsUser(string $user): bool
{
    if (!file_exists('users.json')) {
        return false;
    }

    $usersContent = file_get_contents('users.json');
    $users = json_decode($usersContent, true);

    return isset($users[$user]);
}

function writeMessages($conn, string $currUser, string $currChat): void
{
    $messagesContent = file_get_contents('messages.json');
    $chat = getOrCreateChatId($currUser, $currChat);
    $messages = json_decode($messagesContent, true);

    if (!isset($messages[$chat])) {
        return;
    }

    $messagesFromChat = $messages[$chat];
    if (count($messagesFromChat) > 1) {
        usort($messagesFromChat, static function (array $a, array $b) {
            return $a['t'] <=> $b['t'];
        });
    }

    foreach ($messagesFromChat as $message) {
        fwrite(
            $conn,
            sprintf(
                "[%s] %s \t [%s]" . PHP_EOL,
                trim($message['author']),
                trim($message['msg']),
                (new DateTimeImmutable())->setTimestamp($message['t'])->format("d M y H:i:s")
            )
        );
    }
}

//class ClearMessageFilter extends php_user_filter
//{
//    function filter($in, $out, &$consumed, $closing)
//    {
//        while ($bucket = stream_bucket_make_writeable($in)) {
//            $bucket->data = trim($bucket->data);
//            $consumed += $bucket->datalen;
//            stream_bucket_append($out, $bucket);
//        }
//        return PSFS_PASS_ON;
//    }
//}
