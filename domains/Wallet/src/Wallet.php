<?php

namespace Workshop\Domains\Wallet;

use EventSauce\EventSourcing\AggregateRoot;
use EventSauce\EventSourcing\AggregateRootBehaviour;
use Workshop\Domains\Wallet\Commands\TransferTokens;
use Workshop\Domains\Wallet\Events\TokensDeposited;
use Workshop\Domains\Wallet\Events\TokensWithdrawn;
use Workshop\Domains\Wallet\Events\WithdrawalFailed;
use Workshop\Domains\Wallet\Exceptions\SorryCantWithdraw;
use Workshop\Domains\Wallet\Transactions\Events\TransferInitiated;
use Workshop\Domains\Wallet\Transactions\TransactionId;

class Wallet implements AggregateRoot
{
    use AggregateRootBehaviour;

    private int $tokens = 0;

    public function transfer(TransferTokens $transferTokens, \DateTimeImmutable $now): void
    {
        $this->recordThat(TransferInitiated::fromCommand($transferTokens, $now));
    }

    public function deposit(int $tokens, string $description, \DateTimeImmutable $transactedAt, ?TransactionId $transactionId = null): void
    {
        $this->recordThat(new TokensDeposited($tokens, $description, $transactedAt, $transactionId));
    }

    public function withdraw(int $tokens, string $description, \DateTimeImmutable $transactedAt, ?TransactionId $transactionId = null)
    {
        if($this->tokens < $tokens) {
            $this->recordThat(WithdrawalFailed::becauseOfInsufficientFunds());
            throw SorryCantWithdraw::becauseOfInsufficientFunds();
        }
        $this->recordThat(new TokensWithdrawn($tokens, $description, $transactedAt, $transactionId));
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

    private function applyTransferInitiated(TransferInitiated $event): void
    {
    }
}
