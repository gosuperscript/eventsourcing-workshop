<?php

namespace App\Http\Livewire;

use League\Tactician\CommandBus;
use Livewire\Component;
use Workshop\Domains\FraudDetection\Commands\ApproveTransaction;
use Workshop\Domains\FraudDetection\Queries\GetPendingFraudDetections;

class FraudDetection extends Component
{
    public function approve(string $id, CommandBus $commandBus): void
    {
        $commandBus->handle(new ApproveTransaction(transactionId: $id));
    }

    public function render(GetPendingFraudDetections $getPendingFraudDetections)
    {
        return view('livewire.fraud-detection', [
            'pending' => $getPendingFraudDetections->ask(),
        ]);
    }
}
