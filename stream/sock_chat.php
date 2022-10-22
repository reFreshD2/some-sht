<?php

while (true) {
    $socket = stream_socket_server('tcp://127.0.0.1:8080', $errCode, $errMsg, STREAM_SERVER_LISTEN);
    if (!$socket) {
        echo "[ERROR $errCode] $errMsg";
        return;
    }

    while ($conn = stream_socket_accept($socket, 30)) {
        $message = fread($conn, 1024);
        if (addMessage($message)) {
            fputs($conn, 'Message was written' . PHP_EOL);
        } else {
            fputs($conn, "Message wasn't written" . PHP_EOL);
        }

        fclose($conn);
    }

    fclose($socket);
}


function addMessage(string $message): bool
{
    if (file_exists('messages.json')) {
        $messagesContent = file_get_contents('messages.json');
        if (!$messagesContent) {
            return false;
        }

        $messages = json_decode($messagesContent, true);
        $messages[] = [
            't' => time(),
            'msg' => $message,
        ];

        return file_put_contents('messages.json', json_encode($messages));
    }

    $messages = [
        [
            't' => time(),
            'msg' => $message,
        ],
    ];

    return file_put_contents('messages.json', json_encode($messages));
}