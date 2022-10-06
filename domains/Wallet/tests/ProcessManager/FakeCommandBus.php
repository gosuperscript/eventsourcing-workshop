<?php

namespace Workshop\Domains\Wallet\Tests\ProcessManager;

use League\Tactician\CommandBus;
use PHPUnit\Framework\Assert;

class FakeCommandBus extends CommandBus
{

    private array $commands = [];

    public function __construct()
    {
    }

    public function handle($command)
    {
        $this->commands[] = $command;
    }

    public function assertDispatched($command): void
    {
        Assert::assertTrue(in_array($command, $this->commands, false), 'Command was not dispatched');
    }
}
