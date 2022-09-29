<?php

namespace Workshop\Domains\Wallet\Upcasters;

use EventSauce\EventSourcing\Upcasting\Upcaster;

final class TransactedAtUpcaster implements Upcaster
{
    public function upcast(array $message): array
    {
        if (isset($message['headers']['__time_of_recording'])) {
            $message['payload']['transacted_at'] = $message['headers']['__time_of_recording'];
        }

        if ($message['headers']['__event_type'] == 'workshop.domains.wallet.events.tokens_deposited') {
            $data = include(__DIR__.'/corrections/TokensDeposited.php');
            $corrections = $data['corrections'];

            if (($value = $corrections[$message['headers']['__event_id']] ?? null) !== null) {
                $message['payload']['tokens'] = $value;
            }
        }

        return $message;
    }
}