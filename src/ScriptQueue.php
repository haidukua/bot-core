<?php
declare(strict_types=1);

namespace Haidukua\BotCore;

use Haidukua\BotCore\Contract\ScriptFactoryInterface;
use Haidukua\BotCore\Contract\ScriptInterface;

final class ScriptQueue
{
    private array $queue = [];
    private array $queueNext = [];

    public function __construct(
        private readonly ScriptFactoryInterface $factory,
    ) {}

    public function pop(string $scriptName): void
    {
        $this->queue[] = $scriptName;
    }

    public function popNext(string $scriptName): void
    {
        $this->queueNext[] = $scriptName;
    }

    public function current(): ?ScriptInterface
    {
        $scriptName = $this->fetchCurrentScriptName();

        if ($scriptName === null) {
            return null;
        }

        return $this->factory->create($scriptName);
    }

    public function next(): void
    {
        if (isset($this->queueNext[0])) {
            array_shift($this->queueNext);

            return;
        }

        array_shift($this->queue);
    }

    public function isInQueue(string $scriptName): bool
    {
        return in_array($scriptName, $this->queue, true);
    }

    public function isInQueueNext(string $scriptName): bool
    {
        return in_array($scriptName, $this->queueNext, true);
    }

    private function fetchCurrentScriptName(): ?string
    {
        if (isset($this->queueNext[0])) {
            return $this->queueNext[0];
        }

        if (!isset($this->queue[0])) {
            return null;
        }

        return $this->queue[0];
    }
}
