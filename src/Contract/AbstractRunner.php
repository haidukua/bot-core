<?php
declare(strict_types=1);

namespace Haidukua\BotCore\Contract;

use Haidukua\BotCore\Exception\StopBotException;

abstract class AbstractRunner
{
    /**
     * @throws StopBotException
     */
    abstract public function run(string $heroId): void;

    abstract public function sleepTime(string $heroId): int;

    public function onBoot(string $heroId): void
    {}

    public function onSleep(string $heroId, int $sleepTime): void
    {}

    public function onFinally(string $heroId): void
    {}

    public function onStopped(string $heroId, StopBotException $e): void
    {}

    public function onSignal(string $heroId): void
    {}
}
