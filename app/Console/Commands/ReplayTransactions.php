<?php

namespace App\Console\Commands;

use EventSauce\EventSourcing\OffsetCursor;
use EventSauce\EventSourcing\ReplayingMessages\ReplayMessages;
use Illuminate\Console\Command;
use Workshop\Domains\Wallet\Infra\WalletMessageRepository;
use Workshop\Domains\Wallet\Projectors\TransactionsProjector;

class ReplayTransactions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'replay:transactions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Replay';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(WalletMessageRepository $walletMessageRepository, TransactionsProjector $transactionsProjector)
    {
        $replayMessages = new ReplayMessages(
            repository: $walletMessageRepository,
            consumer: $transactionsProjector,
        );

        $cursor = OffsetCursor::fromStart(limit: 100);

        process_batch:
        $result = $replayMessages->replayBatch($cursor);
        $cursor = $result->cursor();

        if ($result->messagesHandled() > 0) {
            $this->info("processed 100 messages, cursor: {$cursor->toString()}");
            goto process_batch;
        }

        $this->info("done!");
    }
}
