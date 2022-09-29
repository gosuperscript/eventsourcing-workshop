<?php

namespace App\Console\Commands;

use Assert\Assert;
use Illuminate\Console\Command;
use Workshop\Domains\Wallet\Exceptions\SorryCantWithdraw;
use Workshop\Domains\Wallet\Infra\WalletRepository;
use Workshop\Domains\Wallet\WalletId;
use EventSauce\EventSourcing\ReplayingMessages\ReplayMessages;
use EventSauce\EventSourcing\OffsetCursor;
use Workshop\Domains\Wallet\Infra\WalletMessageRepository;
use Workshop\Domains\Wallet\Infra\WalletConsumer;
use Workshop\Domains\Wallet\Projectors\TransactionsProjector;
use Workshop\Domains\Wallet\ReadModels\Transaction;

class Wallet extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wallet:replay';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(WalletRepository $walletRepository)
    {
        Transaction::truncate();

        $replayMessages = new ReplayMessages(
            app(WalletMessageRepository::class),
            app(TransactionsProjector::class),
        );
        
        $cursor = OffsetCursor::fromStart(limit: 100);
        
        $result = $replayMessages->replayBatch($cursor);
        $cursor = $result->cursor();
        
        process_batch:
        if ($result->messagesHandled() > 0) {
            foreach($cursor as $entry) {
                goto process_batch;
            }
        }
    }
}
