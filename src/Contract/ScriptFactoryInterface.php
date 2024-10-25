<?php
namespace Haidukua\BotCore\Contract;

interface ScriptFactoryInterface
{
    public function create(string $scriptName): ScriptInterface;
}
