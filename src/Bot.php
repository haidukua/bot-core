<?php
declare(strict_types=1);

namespace Haidukua\BotCore;

use Haidukua\BotCore\Contract\AbstractRunner;

final readonly class Bot
{
    public function __construct(
        private AbstractRunner $runner,
        private Scheduler $scheduler,
    ) {
    }

    public function run(string $heroId): void
    {
        $this->runner->onBoot($heroId);

        try {
            $this->runner->run($heroId);
        } catch (Exception\StopBotException) {
            $this->runner->onStopped($heroId);

            return;
        } finally {
            $this->runner->onFinally($heroId);
        }

        $this->scheduler->add($heroId, $this->runner->sleepTime($heroId));
    }
}
