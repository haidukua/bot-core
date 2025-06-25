<?php
declare(strict_types=1);

namespace Haidukua\BotCore;

use Haidukua\BotCore\Contract\AbstractRunner;

final readonly class Bot
{
    public function __construct(
        private AbstractRunner $runner,
        private Control $control,
    ) {
    }

    public function run(string $heroId): void
    {
        try {
            $this->runner->onBoot($heroId);
            $this->runner->run($heroId);

            throw new Exception\SleepException();
        } catch (Exception\StopBotException $e) {
            $this->runner->onStopped($heroId, $e);
            $this->control->stop($heroId);

            return;
        } catch (Exception\SleepException $e) {
            $sleepTime = $this->runner->sleepTime($heroId);
            $this->runner->onSleep($heroId, $sleepTime);

            $this->control->start($heroId, $sleepTime);

            return;
        } finally {
            $this->runner->onFinally($heroId);
        }
    }
}
