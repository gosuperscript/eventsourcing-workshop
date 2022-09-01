<?php

namespace Workshop\Domains\Wallet;

enum WithdrawalFailureType: string
{
    case InsufficientFunds = 'insufficient_funds';
}
