<?php

namespace Workshop\Domains\Wallet\Upcasters;

use EventSauce\EventSourcing\Header;
use EventSauce\EventSourcing\Upcasting\Upcaster;

class TransactedAtUpcaster implements Upcaster
{

    public function upcast(array $message): array
    {
        $eventType = $message['headers'][Header::EVENT_TYPE];
        if($eventType !== 'tokens_deposited' && $eventType !== 'tokens_withdrawn') {
            return $message;
        }
        if (isset($message['payload']['transacted_at'])) {
            return $message;
        }
        $message['payload']['transacted_at'] = $message['headers']['__time_of_recording'];
        return $message;
    }
}
