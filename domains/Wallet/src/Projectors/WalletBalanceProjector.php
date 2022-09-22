<?php

namespace Workshop\Domains\Wallet\Projectors;

use EventSauce\EventSourcing\EventConsumption\EventConsumer;
use EventSauce\EventSourcing\Message;
use Workshop\Domains\Wallet\Events\TokensDeposited;
use Workshop\Domains\Wallet\Events\TokensWithdrawn;
use Workshop\Domains\Wallet\Infra\WalletBalanceRepository;

class WalletBalanceProjector extends EventConsumer
{
    public function __construct(private WalletBalanceRepository $walletReadRepository)
    {
    }

    public function handleTokensDeposited(TokensDeposited $tokensDeposited, Message $message)
    {
        $this->walletReadRepository->updateWalletTokens($message->aggregateRootId(), $tokensDeposited->balance);
    }

    public function handleTokensWithdrawn(TokensWithdrawn $tokensWithdrawn, Message $message)
    {
        $this->walletReadRepository->updateWalletTokens($message->aggregateRootId(), $tokensWithdrawn->balance);
    }
}
