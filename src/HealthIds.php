<?php
declare(strict_types=1);

namespace Haidukua\BotCore;

final readonly class HealthIds
{
    public function __construct(
        public array $normal,
        public array $priority,
    ) {}
}
