<?php
declare(strict_types=1);

namespace Haidukua\BotCore;

final readonly class Scheduler
{
    private int $stopAt;

    public function __construct(
        private \Redis $redis,
        private Terminal $terminal,
        private string $queueKey = 'bot:queue',
        private int $timeLimit = 3600,
    )
    {
        $this->stopAt = time() + $this->timeLimit;
    }

    public function run(): void
    {
        $now = time();

        if ($this->stopAt < $now) {
            return;
        }

        $queuedMessageCount = $this->redis->zCount($this->queueKey, '0', (string) $now) ?? 0;

        $messages = $this->redis->zPopMin($this->queueKey, $queuedMessageCount) ?? [];

        if ($queuedMessageCount !== 0) {
            foreach ($messages as $heroId => $expiry) {
                if ($now - $expiry > 30) {
                    continue;
                }

                $this->terminal->stopBotProcess($heroId);
                $this->terminal->runCommandBackground($heroId);
            }
        }

        sleep(5);

        $this->run();
    }

    public function add(string $heroId, int $delay = 0): void
    {
        $delayInSec = time() + $delay;

        $this->redis->zAdd($this->queueKey,$delayInSec, $heroId);
    }

    public function remove(string $heroId): void
    {
        $this->redis->zRem($this->queueKey,$heroId);
    }

    public function removeAll(): void
    {
        $this->redis->del($this->queueKey);
    }
}
