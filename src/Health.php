<?php
declare(strict_types=1);

namespace Haidukua\BotCore;

final readonly class Health
{
    public function __construct(
        private \Redis $redis,
        private string $prefix = 'bot'
    ) {}

    public function normal(string $heroId, \DateTimeImmutable $pingTime = new \DateTimeImmutable()): void
    {

        $this->redis->zAdd($this->prefix . ':health', $pingTime->getTimestamp(), $heroId);
    }

    public function priority(string $heroId, \DateTimeImmutable $pingTime = new \DateTimeImmutable()): void
    {
        $this->redis->zAdd($this->prefix . ':health', $pingTime->getTimestamp() + 0.1, $heroId);
    }

    public function remove(string $heroId): void
    {
        $this->redis->zRem($this->prefix . ':health',$heroId);
    }

    public function fetchIds(\DateTimeImmutable $toPingTime): HealthIds
    {
        $rows = $this->redis->zRangeByScore(
            $this->prefix . ':health',
            '0',
            (string) $toPingTime->getTimestamp(),
            ['WITHSCORES' => true]
        ) ?? [];

        $priority = [];
        $normal = [];
        foreach ($rows as $key => $value) {
            if ($key % 2 === 0) {
                continue;
            }

            if (str_ends_with($rows[$key + 1], '.1')) {
                $priority[] = $value;

                continue;
            }

            $normal[] = $value;
        }

        return new HealthIds($normal, $priority);
    }
}
