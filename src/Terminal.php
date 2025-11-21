<?php
declare(strict_types=1);

namespace Haidukua\BotCore;

final readonly class Terminal
{
    public function __construct(private string $path)
    {
    }

    public function stopAllBotProcesses(): void
    {
        $command = sprintf(
            "pkill -15 -f 'php %s'",
            $this->path
        );

        $this->execute($command);
    }

    public function stopBotProcess(string $heroId): void
    {
        $command = sprintf(
            "pkill -15 -f 'php %s %s'",
            $this->path,
            $heroId
        );

        $this->execute($command);
    }

    public function runCommandBackground($heroId): void
    {
        $command = sprintf(
            'php %s %s > /dev/null 2>&1 &',
            $this->path,
            $heroId,
        );

        $this->execute($command);
    }

    private function execute(string $command): void
    {
        exec($command);
    }
}
