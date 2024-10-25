<?php
declare(strict_types=1);

namespace Haidukua\BotCore;

use Haidukua\BotCore\Contract\ScriptFactoryInterface;
use Haidukua\BotCore\Contract\ScriptInterface;

final class ScriptLauncher
{
    private array $queue = [];
    private array $nextQueue = [];

    public function __construct(
        private readonly ScriptFactoryInterface $factory,
    ) {}

    public function add(string $scriptName): void
    {
        $this->queue[] = $scriptName;
    }

    public function addNext(string $scriptName): void
    {
        $this->nextQueue[] = $scriptName;
    }

    public function current(): ?ScriptInterface
    {
        $scriptName = current($this->queue);

        if (!empty($this->nextQueue)) {
            $scriptName = current($this->nextQueue);
        }

        if (!$scriptName) {
            return null;
        }

        return $this->factory->create($scriptName);
    }

    public function next(): void
    {
        if (!empty($this->nextQueue)) {
            next($this->nextQueue);

            return;
        }

        next($this->queue);
    }
}
