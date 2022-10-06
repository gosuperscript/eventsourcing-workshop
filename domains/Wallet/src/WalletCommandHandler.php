<?php

namespace Workshop\Domains\Wallet;

use Carbon\Carbon;
use EventSauce\Clock\Clock;
use Workshop\Domains\Wallet\Commands\DepositTokens;
use Workshop\Domains\Wallet\Commands\WithdrawTokens;
use Workshop\Domains\Wallet\Infra\WalletRepository;

class WalletCommandHandler
{
    public function __construct(
        private WalletRepository $walletRepository,
        private Clock $clock,
    )
    {
    }

    public function handleWithdrawTokens(WithdrawTokens $withdrawTokens): void
    {
        $wallet = $this->walletRepository->retrieve($withdrawTokens->walletId);
        $wallet->withdraw(
            $withdrawTokens->tokens,
            $withdrawTokens->description,
            Carbon::parse($this->clock->now()),
            $withdrawTokens->transactionId
        );
        $this->walletRepository->persist($wallet);
    }

    public function handleDepositTokens(DepositTokens $depositTokens): void
    {
        $wallet = $this->walletRepository->retrieve($depositTokens->walletId);
        $wallet->deposit(
            $depositTokens->tokens,
            $depositTokens->description,
            Carbon::parse($this->clock->now()),
            $depositTokens->transactionId
        );
        $this->walletRepository->persist($wallet);
    }
}
