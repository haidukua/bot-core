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

    public function add(string $scriptClass): void
    {
        if (in_array($scriptClass, $this->failed, true)) {
            return;
        }

        $this->queue[] = $scriptClass;
    }

    public function addNext(string $scriptClass): void
    {
         if (in_array($scriptClass, $this->failed, true)) {
            return;
         }

        $this->queueNext[] = $scriptClass;
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

    public function fail(string $scriptClass): void
    {
        if (in_array($scriptClass, $this->queue, true)) {
            return;
        }

        $this->failed[] = $scriptClass;
    }

    /**
     * @param class-string $scriptClass
     */
    public function isInQueue(string $scriptClass): bool
    {
        return in_array($scriptClass, $this->queue, true);
    }

    /**
     * @param class-string $scriptClass
     */
    public function isInQueueNext(string $scriptClass): bool
    {
        return in_array($scriptClass, $this->queueNext, true);
    }
}
