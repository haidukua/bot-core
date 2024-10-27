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
        $nextScriptName = current($this->nextQueue);

        if ($nextScriptName !== false) {
            return $this->factory->create($nextScriptName);
        }

        $scriptName = current($this->queue);

        if (!$scriptName) {
            return null;
        }

        return $this->factory->create($scriptName);
    }

    public function next(): void
    {
        $result = next($this->nextQueue);

        if ($result !== false) {
            return;
        }

        next($this->queue);
    }
}
