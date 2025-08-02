<?php
declare(strict_types=1);

namespace Haidukua\BotCore;

final class ScriptQueue
{
    /**
     * @var class-string|null
     */
    private(set) ?string $processingScript = null;

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

    public function peek(): ?string
    {
        if (isset($this->priority[0])) {
            return $this->processingScript = $this->priority[0];
        }

        if (isset($this->regular[0])) {
            return $this->processingScript = $this->regular[0];
        }

        return null;
    }

    public function done(): void
    {
        if (isset($this->priority[0]) && $this->priority[0] === $this->processingScript) {
            array_shift($this->priority);
        }

        if (isset($this->regular[0]) && $this->regular[0] === $this->processingScript) {
            array_shift($this->regular);
        }

        $this->processingScript = null;
    }

    public function fail(): void
    {
        if ($this->processingScript === null) {
            return;
        }

        $this->done();

        if (in_array($this->processingScript, $this->failed, true)) {
            return;
        }

        $this->failed[] = $this->processingScript;
    }
}