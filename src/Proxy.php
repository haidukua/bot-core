<?php
declare(strict_types=1);

namespace Haidukua\BotCore;

final class Proxy
{
    public function __construct(
        public readonly string $id,
        public readonly string $login,
        public readonly string $password,
        public readonly string $ip,
        public readonly int $port,
        public \DateTimeInterface $usedAt = new \DateTimeImmutable(),
        public \DateTimeInterface $failedAt = new \DateTimeImmutable(),
    ) {
    }
}
