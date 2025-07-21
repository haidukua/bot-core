<?php
declare(strict_types=1);

namespace Haidukua\BotCore;

final class ScriptQueue
{
    private ?string $current = null;
    /**
     * @var class-string[]
     */
    private array $queue = [];

    /**
     * @var class-string[]
     */
    private array $queueNext = [];

    /**
     * @var class-string[]
     */
    private array $failed = [];

    public function add(string $scriptName): void
    {
        if (in_array($scriptName, $this->failed, true)) {
            return;
        }

        $this->queue[] = $scriptName;
    }

    public function addNext(object $scriptName): void
    {
         if (in_array($scriptName, $this->failed, true)) {
            return;
         }

        $this->queueNext[] = $scriptName;
    }

    public function current(): ?string
    {
        if ($this->current === null) {
            if (isset($this->queueNext[0])) {
                return $this->current = $this->queueNext[0];
            }

            if (isset($this->queue[0])) {
                return $this->current = $this->queue[0];
            }
        }

        return $this->current;
    }

    public function next(): void
    {
        if ($this->current !== null && isset($this->queueNext[0]) && $this->queueNext[0] === $this->current) {
            array_shift($this->queueNext);
        }

        if (!isset($this->queueNext[0])) {
            array_shift($this->queue);
        }

        $this->current = null;
    }

    public function fail(string $scriptName): void
    {
        if (in_array($scriptName, $this->queue, true)) {
            return;
        }

        $this->failed[] = $scriptName;
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
