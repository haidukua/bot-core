<?php
declare(strict_types=1);

namespace Haidukua\BotCore;

final class ScriptQueue
{
    /**
     * @var object[]
     */
    private array $queue = [];

    /**
     * @var object[]
     */
    private array $queueNext = [];

    public function add(object $script): void
    {
        $this->queue[] = $script;
    }

    public function addNext(object $script): void
    {
        $this->queueNext[] = $script;
    }

    public function current(): ?object
    {
        if (isset($this->queueNext[0])) {
            return $this->queueNext[0];
        }

        if (isset($this->queue[0])) {
            return $this->queue[0];
        }

        return null;
    }

    public function next(): void
    {
        if (isset($this->queueNext[0])) {
            array_shift($this->queueNext);

            return;
        }

        array_shift($this->queue);
    }

    /**
     * @param class-string $scriptClass
     */
    public function isInQueue(string $scriptClass): bool
    {
        foreach ($this->queue as $script) {
            if ($script instanceof  $scriptClass) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param class-string $scriptClass
     */
    public function isInQueueNext(string $scriptClass): bool
    {
        foreach ($this->queueNext as $script) {
            if ($script instanceof $scriptClass) {
                return true;
            }
        }

        return false;
    }
}
