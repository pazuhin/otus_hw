<?php

namespace App;

use AllowDynamicProperties;
use App\command\CommandInterface;
use Closure;
use SplQueue;

#[AllowDynamicProperties]
final class ServerThread
{
    private SplQueue $queue;
    private Closure $behaviour;
    private bool $stop = false;

    public function __construct() {
        $this->queue = new SplQueue();

        $this->behaviour = function () {
            if ($this->queue->isEmpty()) {
                usleep(100_000);
                return;
            }

            $cmd = $this->queue->dequeue();
            try {
                $cmd->execute();
            } catch (\Throwable $e) {
                echo "Exception caught: " . $e->getMessage() . PHP_EOL;
            }
        };
    }

    public function run(): void
    {
        while (!$this->stop) {
            ($this->behaviour)();
        }
    }

    public function stop(): void
    {
        $this->stop = true;
    }

    public function updateBehaviour(Closure $newBehaviour): void {
        $this->behaviour = $newBehaviour;
    }

    public function hasCommands(): bool {
        return !$this->queue->isEmpty();
    }

    public function getQueue(): SplQueue {
        return $this->queue;
    }

    /**
     * @return Closure
     */
    public function getBehaviuor(): Closure
    {
        return $this->behaviour;
    }

    public function addCommand(CommandInterface $command): void
    {
        $this->queue->enqueue($command);
    }
}