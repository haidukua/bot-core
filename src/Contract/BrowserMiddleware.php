<?php

namespace Haidukua\BotCore\Contract;

use Haidukua\BotCore\Browser;

abstract class BrowserMiddleware
{
    public function preRequest(Browser $browser): void
    {}

    public function postRequest(Browser $browser): void
    {}
}
