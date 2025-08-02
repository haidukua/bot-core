<?php
declare(strict_types=1);

namespace Haidukua\BotCore;

final class ScriptQueue
{
    /**
     * @var class-string[]
     */
    private array $regular = [];

    /**
     * @var class-string[]
     */
    private array $priority = [];

    /**
     * @var class-string[]
     */
    private array $failed = [];

    public function add(string $scriptClass): void
    {
        if (in_array($scriptClass, $this->failed, true)) {
            return;
        }

        $this->regular[] = $scriptClass;
    }

    public function addPriority(string $scriptClass): void
    {
         if (in_array($scriptClass, $this->failed, true)) {
            return;
         }

        $this->priority[] = $scriptClass;
    }

    public function current(): ?string
    {
        if (!empty($this->priority)) {
            return $this->priority[0];
        }

        if (!empty($this->regular)) {
            return $this->regular[0];
        }

        return null;
    }

    public function next(): void
    {
        if (!empty($this->priority)) {
            array_shift($this->priority);
            return;
        }

        if (!empty($this->regular)) {
            array_shift($this->regular);
            return;
        }
    }

    public function fail(string $scriptClass): void
    {
        if (in_array($scriptClass, $this->failed, true)) {
            return;
        }

        $this->failed[] = $scriptClass;
    }

    /**
     * @param class-string $scriptClass
     */
    public function isInRegular(string $scriptClass): bool
    {
        return in_array($scriptClass, $this->regular, true);
    }

    /**
     * @param class-string $scriptClass
     */
    public function isInPriority(string $scriptClass): bool
    {
        return in_array($scriptClass, $this->priority, true);
    }
}
