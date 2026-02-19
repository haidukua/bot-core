<?php
declare(strict_types=1);

namespace Haidukua\BotCore;

final readonly class Control
{
    public function __construct(
        private Scheduler $scheduler,
        private Health $health,
    ) {}

    public function start(string $heroId, int $delay = 0): void
    {
        $this->scheduler->add($heroId, $delay);
        $this->health->normal(
            $heroId,
            new \DateTimeImmutable()->modify(sprintf('+ %s seconds', $delay))
        );
    }

    public function stop(string $heroId): void
    {
        $this->scheduler->remove($heroId);
        $this->health->remove($heroId);
    }
}
