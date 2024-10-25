<?php
declare(strict_types=1);

namespace Haidukua\BotCore\Contract;

use Haidukua\BotCore\Exception\LogicScriptException;
use Haidukua\BotCore\Exception\StopBotException;
use Haidukua\BotCore\Exception\NextScriptException;

abstract class AbstractRunner
{
    /**
     * @throws StopBotException
     */
    abstract public function run(string $heroId): void;

    abstract public function sleepTime(string $heroId): int;

    public function onBoot(string $heroId): void
    {}

    public function onFinally(string $heroId): void
    {}

    public function onStopped(string $heroId): void
    {}
}
