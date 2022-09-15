<?php

namespace App\Console\Commands;

use Assert\Assert;
use EventSauce\Clock\Clock;
use Illuminate\Console\Command;
use Workshop\Domains\Wallet\Exceptions\SorryCantWithdraw;
use Workshop\Domains\Wallet\Infra\WalletRepository;
use Workshop\Domains\Wallet\WalletId;

class Wallet extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wallet';

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
    public function handle(WalletRepository $walletRepository, Clock $clock)
    {
        $action = $this->choice(
            'Deposit or withdraw?',
            ['Deposit', 'Withdraw'],
        );
        if ($this->confirm('Already existing wallet?')) {
            $walletId = WalletId::fromString($this->anticipate('Whats the id of the wallet?', []));
        } else {
            $walletId = WalletId::generate();
        }

        $tokens = (int) $this->ask("amount of tokens?");
        Assert::that($tokens)->integer()->greaterThan(0);

        $wallet = $walletRepository->retrieve($walletId);
        try {
            if($action === 'Deposit'){
                $wallet->deposit($tokens, $this->ask("description?"), $clock->now());
            } else {
                $wallet->withdraw($tokens, $this->ask("description?"), $clock->now());
            }
        } catch (SorryCantWithdraw $exception) {
            $this->error($exception->getMessage());
            return 1;
        } finally {
            $walletRepository->persist($wallet);
        }

        $this->info("✅ Tokens successfully {$action}d, to wallet {$walletId->toString()}");
        return 0;
    }
}
