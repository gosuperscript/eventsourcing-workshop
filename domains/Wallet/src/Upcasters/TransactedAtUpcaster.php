<?php

namespace Workshop\Domains\Wallet\Upcasters;

use EventSauce\EventSourcing\Header;
use EventSauce\EventSourcing\Upcasting\Upcaster;

class TransactedAtUpcaster implements Upcaster
{
    public function upcast(array $message): array
    {
        $eventType = $message['headers'][Header::EVENT_TYPE];

        if (! in_array($eventType, ['tokens_deposited', 'tokens_withdrawn'])) {
            return $message;
        }

        $message['payload']['transacted_at'] ??= $message['headers'][Header::TIME_OF_RECORDING];

        return $message;
    }
}
