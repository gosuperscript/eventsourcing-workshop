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
        $tokens = $this->walletReadRepository->getWalletTokens($message->aggregateRootId());
        $this->walletReadRepository->updateWalletTokens($message->aggregateRootId(), $tokens + $tokensDeposited->tokens);
    }

    public function handleTokensWithdrawn(TokensWithdrawn $tokensWithdrawn, Message $message)
    {
        $tokens = $this->walletReadRepository->getWalletTokens($message->aggregateRootId());
        $this->walletReadRepository->updateWalletTokens($message->aggregateRootId(), $tokens - $tokensWithdrawn->tokens);
    }
}
