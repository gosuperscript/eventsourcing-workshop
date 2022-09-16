<?php

namespace Workshop\Domains\Wallet\Upcasters;

use EventSauce\EventSourcing\Header;
use EventSauce\EventSourcing\Upcasting\Upcaster;

class BalanceUpcaster implements Upcaster
{
    public array $balance = [];
    public function upcast(array $message): array
    {
        $eventType = $message['headers'][Header::EVENT_TYPE];
        if(!$this->shouldHandleEvent($eventType)) {
            return $message;
        }
        if($message['headers'][Header::AGGREGATE_ROOT_VERSION === 0])
        if (isset($message['payload']['transacted_at'])) {
            return $message;
        }
        $message['payload']['transacted_at'] = $message['headers']['__time_of_recording'];
        return $message;
    }

    private function shouldHandleEvent(string $eventType): bool
    {
        return $eventType === 'tokens_deposited' || $eventType === 'tokens_withdrawn';
    }
}
