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

        $aggregateId = $message['headers'][Header::AGGREGATE_ROOT_ID];
        if($message['headers'][Header::AGGREGATE_ROOT_VERSION] === 1){
            $this->balance[$aggregateId] = 0;
        }

        if(!$this->shouldHandleEvent($eventType)) {
            return $message;
        }

        if(array_key_exists('balance', $message['payload'])) {
            return $message;
        }

        if($eventType === 'tokens_deposited'){
            $this->balance[$aggregateId] += $message['payload']['tokens'];
        }

        if($eventType === 'tokens_withdrawn'){
            $this->balance[$aggregateId] -= $message['payload']['tokens'];
        }

        $message['payload']['balance'] = $this->balance[$aggregateId];
        return $message;
    }

    private function shouldHandleEvent(string $eventType): bool
    {
        return $eventType === 'tokens_deposited' || $eventType === 'tokens_withdrawn';
    }
}
