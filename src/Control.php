<?php
declare(strict_types=1);

namespace Haidukua\BotCore;

final readonly class Control
{
    public function __construct(
        private Terminal $terminal,
        private Scheduler $scheduler,
    ) {}

    public function start(string $heroId, int $delay = 0): void
    {
        $this->terminal->stopBotProcess($heroId);
        $this->scheduler->add($heroId, $delay);
    }

    public function stop(string $heroId): void
    {
        $this->terminal->stopBotProcess($heroId);
        $this->scheduler->remove($heroId);
    }

    public function stopAll(): void
    {
        $this->terminal->stopAllBotProcesses();
        $this->scheduler->removeAll();
    }
}
