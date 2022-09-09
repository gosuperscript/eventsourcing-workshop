<?php

namespace Workshop\Domains\Wallet\Tests;

use EventSauce\EventSourcing\Message;
use EventSauce\EventSourcing\MessageDispatcher;

class IntegrationTestMessageDispatcher implements MessageDispatcher
{
    /** @var Message[] */
    private array $received = [];

    public static function instance(): self
    {
        static $instance;
        return $instance ?: $instance = new self;
    }

    public function dispatch(Message ...$messages): void
    {
        foreach ($messages as $message) {
            $this->received[] = $message;
        }
    }

    /**
     * @return Message[]
     */
    public function received(): array
    {
        return $this->received;
    }
}