<?php

namespace Haidukua\BotCore\Contract;

use Symfony\Component\BrowserKit\Request;

abstract class BrowserFilter
{
    public function filterRequest(Request $request): Request
    {
        return $request;
    }

    public function filterResponse(object $response): object
    {
        return $response;
    }
}
