<?php

declare(strict_types=1);

namespace App\Application\Chat;

use RuntimeException;

class Server
{
    /**
     * @var resource
     */
    private $socket;

    public function __construct(
        private string $serverPath
    ) {
    }

    /**
     * @return false|resource
     */
    public function start() {
        $socket = stream_socket_server($this->serverPath, $errCode, $errMsg);
        if (!$socket) {
            throw new RuntimeException("[$errCode] Cant start server - $errMsg");
        }

        $this->socket = $socket;
        return stream_socket_accept($this->socket, -1);
    }

    public function close($channel): void
    {
        fwrite($channel, 'Bye bye ...' . PHP_EOL);
        fclose($channel);
        fclose($this->socket);
    }
}
