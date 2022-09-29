<?php

namespace Workshop\Domains\Wallet\Infra;

use EventSauce\EventSourcing\Message;
use EventSauce\EventSourcing\MessageConsumer;
use Workshop\Domains\Wallet\Wallet;

class WalletConsumer implements MessageConsumer
{
    public function handle(Message $message): void
    {
        $wallet = new Wallet();
        $wallet
    }
}