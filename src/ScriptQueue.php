<?php
declare(strict_types=1);

namespace Haidukua\BotCore;

final class ScriptQueue
{
    /**
     * @var class-string[]
     */
    private array $queue = [];

    /**
     * @var class-string[]
     */
    private array $queueNext = [];

    /**
     * @param class-string $scriptName
     */
    public function add(string $scriptName): void
    {
        $this->queue[] = $scriptName;
    }

    /**
     * @param class-string $scriptName
     */
    public function addNext(string $scriptName): void
    {
        $this->queueNext[] = $scriptName;
    }


    /**
     * @return class-string|null
     */
    public function current(): ?string
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
     * @param class-string $scriptName
     */
    public function isInQueue(string $scriptName): bool
    {
        return in_array($scriptName, $this->queue, true);
    }

    /**
     * @param class-string $scriptName
     */
    public function isInQueueNext(string $scriptName): bool
    {
        return in_array($scriptName, $this->queueNext, true);
    }
}
