<?php

declare(strict_types=1);

namespace Haidukua\BotCore\Exception;

final class SleepException extends \Exception implements ExceptionInterface
{
    public ?int $sleepTime;

    public function __construct(?int $sleepTime = null, string $message = "", int $code = 0, ?\Throwable $previous = null)
    {
        $this->sleepTime = $sleepTime;
        parent::__construct($message, $code, $previous);
    }
}