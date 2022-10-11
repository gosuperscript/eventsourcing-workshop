<?php

namespace Workshop\Domains\FraudDetection\Queries;

use Workshop\Domains\FraudDetection\DTO\FraudDetectionReadModel;
use Workshop\Domains\FraudDetection\FraudDetection;

class GetPendingFraudDetections
{
    public function ask(): array
    {
        return FraudDetection::query()->whereNull(['approved_at', 'rejected_at'])->get()
            ->map(fn(FraudDetection $fraudDetection) => new FraudDetectionReadModel($fraudDetection->transaction_id, $fraudDetection->tokens))
            ->toArray();
    }
}
