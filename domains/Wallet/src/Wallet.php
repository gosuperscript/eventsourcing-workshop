<?php

namespace Workshop\Domains\Wallet;

use Carbon\Carbon;
use EventSauce\EventSourcing\AggregateRoot;
use EventSauce\EventSourcing\AggregateRootBehaviour;
use Workshop\Domains\Wallet\Events\TokensDeposited;
use Workshop\Domains\Wallet\Events\TokensWithdrawn;
use Workshop\Domains\Wallet\Events\TransferInitiated;
use Workshop\Domains\Wallet\Events\WithdrawalFailed;
use Workshop\Domains\Wallet\Exceptions\SorryCantWithdraw;
use Workshop\Domains\Wallet\Transactions\TransactionId;

class Wallet implements AggregateRoot
{
    use AggregateRootBehaviour;

    private int $tokens = 0;

    public function deposit(int $tokens, string $description, Carbon $transactedAt, ?TransactionId $transactionId = null)
    {
        $this->recordThat(new TokensDeposited(
            tokens: $tokens,
            transactedAt: $transactedAt,
            transactionId: $transactionId,
            description: $description,
        ));
    }

    public function withdraw(int $tokens, string $description, Carbon $transactedAt, ?TransactionId $transactionId = null)
    {
        if ($this->tokens < $tokens) {
            $this->recordThat(WithdrawalFailed::becauseOfInsufficientFunds());
            throw SorryCantWithdraw::becauseOfInsufficientFunds();
        }
        $this->recordThat(new TokensWithdrawn(
            tokens: $tokens,
            transactedAt: $transactedAt,
            transactionId: $transactionId,
            description: $description,
        ));
    }

    public function transfer(TransactionId $transactionId, WalletId $receivingWalletId, int $tokens, string $description, Carbon $startedAt,)
    {
        if ($this->tokens < $tokens) {
            return;
        }

        $this->recordThat(new TransferInitiated(
            transactionId: $transactionId,
            receivingWalletId: $receivingWalletId,
            tokens: $tokens,
            description: $description,
            startedAt: $startedAt,
        ));
    }

    private function applyTokensDeposited(TokensDeposited $event): void
    {
        $this->tokens += $event->tokens;
    }

    private function applyTokensWithdrawn(TokensWithdrawn $event): void
    {
        $this->tokens -= $event->tokens;
    }

    private function applyWithdrawalFailed(WithdrawalFailed $event): void
    {
    }
}
