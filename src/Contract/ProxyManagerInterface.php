<?php

namespace Haidukua\BotCore\Contract;

use Haidukua\BotCore\Proxy;

interface ProxyManagerInterface
{
    public function get(): ?Proxy;

    public function fail(Proxy $proxy): void;
}
