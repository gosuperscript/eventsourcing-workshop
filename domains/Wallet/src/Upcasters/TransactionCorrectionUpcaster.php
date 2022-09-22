<?php

namespace Workshop\Domains\Wallet\Upcasters;

use EventSauce\EventSourcing\Header;
use EventSauce\EventSourcing\Upcasting\Upcaster;

class TransactionCorrectionUpcaster implements Upcaster
{
    public function __construct(
        /** @var array<string, int> */
        private array $correctionsMap,
    )
    {
    }

    public function upcast(array $message): array
    {
        if (! $this->shouldHandle($message)) {
            return $message;
        }

        $transactionId = $message['headers'][Header::EVENT_ID];

        if (! $newAmount = $this->correctionsMap[$transactionId] ?? null) {
            return $message;
        }

        $message['payload']['tokens'] = $newAmount;

        return $message;
    }

    private function shouldHandle(array $message): bool
    {
        return in_array($message['headers'][Header::EVENT_TYPE], ['tokens_deposited', 'tokens_withdrawn']);
    }
}
