<?php

namespace Workshop\Domains\FraudDetection;


use EventSauce\EventSourcing\Header;
use EventSauce\EventSourcing\Message;
use Workshop\Domains\FraudDetection\Commands\ApproveTransaction;
use Workshop\Domains\FraudDetection\Commands\RejectTransaction;
use Workshop\Domains\FraudDetection\Commands\RequestFraudDetection;
use Workshop\Domains\FraudDetection\Events\TransactionApproved;
use Workshop\Domains\FraudDetection\Events\TransactionRejected;
use Workshop\Domains\ProcessManager\ProcessHeaders;
use Workshop\Domains\ProcessManager\ProcessManagerReactor;

class FraudCommandHandler
{
    public function handleRequestFraudDetection(RequestFraudDetection $requestFraudDetection): void
    {
        FraudDetection::create([
            'transaction_id' => $requestFraudDetection->transactionId,
            'tokens' => $requestFraudDetection->tokens,
        ]);
    }

    public function handleApproveTransaction(ApproveTransaction $approveTransaction): void
    {
        $detection = FraudDetection::where('transaction_id', $approveTransaction->transactionId)->firstOrFail();
        if($detection->approved_at !== null || $detection->rejected_at !== null){
            throw new \Exception("transaction already approved or rejected");
        }
        $detection->update([
            'approved_at' => now(),
        ]);

        $this->publishOnEventBus(
            new TransactionApproved($approveTransaction->transactionId)
        );
    }

    public function handleRejectTransaction(RejectTransaction $rejectTransaction): void
    {
        $detection = FraudDetection::where('transaction_id', $rejectTransaction->transactionId)->firstOrFail();
        if($detection->approved_at !== null || $detection->rejected_at !== null){
            throw new \Exception("transaction already approved or rejected");
        }
        $detection->update([
            'approved_at' => now(),
        ]);


        $this->publishOnEventBus(
            new TransactionRejected($rejectTransaction->transactionId)
        );
    }

    private function publishOnEventBus(TransactionRejected | TransactionApproved $event)
    {
        // Normally an event would be published on the public event bus, causing it to be picked up by the ProcessManagers.
        // For the sake of shortcuts and simplicity, we call the process manager manually.
        /** @var ProcessManagerReactor $processManagerReactor */
        $processManagerReactor = resolve(ProcessManagerReactor::class);
        $processManagerReactor->handle(
            (new Message(
                $event
            ))->withHeaders([
                ProcessHeaders::CORRELATION_ID => $event->transactionId
            ])
        );
    }
}
