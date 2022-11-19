<?php

declare(strict_types=1);

namespace App\Application\Chat;

use RuntimeException;

class Server
{
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

        return stream_socket_accept($socket, -1);
    }
}
