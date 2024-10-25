<?php

namespace Haidukua\BotCore\Contract;

interface PingStorageInterface
{
    public function set(string $id, string $message);

    public function get(string $id);
}
