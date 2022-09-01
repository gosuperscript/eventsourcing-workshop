<?php

namespace Workshop\Domains\Wallet;

use App\Uuid;
use EventSauce\EventSourcing\AggregateRootId;

final class WalletId extends Uuid implements AggregateRootId
{
}
