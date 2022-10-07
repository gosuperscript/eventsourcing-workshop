<?php

namespace Workshop\Domains\Wallet\Transactions;

use App\Uuid;
use EventSauce\EventSourcing\AggregateRootId;

class TransactionId extends Uuid implements AggregateRootId
{

}
