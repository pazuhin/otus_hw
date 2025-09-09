<?php

namespace App\command;

use App\ServerThread;

readonly class HardStopCommand implements CommandInterface {
    public function __construct(private ServerThread $server) {}

    public function execute(): void {
        $this->server->stop();
    }
}