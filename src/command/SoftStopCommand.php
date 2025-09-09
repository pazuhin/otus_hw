<?php

namespace App\command;

use App\ServerThread;

readonly class SoftStopCommand implements CommandInterface {
    public function __construct(private ServerThread $server) {}

    public function execute(): void
    {
        $oldBehaviour = $this->server->getBehaviuor();
        $server = $this->server;

        $this->server->updateBehaviour(function () use ($oldBehaviour, $server) {
            if ($server->hasCommands()) {
                $oldBehaviour();
            } else {
                $server->stop();
            }
        });
    }
}