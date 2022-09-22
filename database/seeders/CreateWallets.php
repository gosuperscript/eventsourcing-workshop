<?php

namespace Database\Seeders;

use EventSauce\Clock\SystemClock;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Ramsey\Uuid\Uuid;
use Workshop\Domains\Wallet\Exceptions\SorryCantWithdraw;
use Workshop\Domains\Wallet\Infra\WalletRepository;
use Workshop\Domains\Wallet\Wallet;
use Workshop\Domains\Wallet\WalletId;

class CreateWallets extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $walletIds = [];
        foreach (range(0, 500) as $index){
            $walletIds[] = WalletId::generate();
        }

        $clock = new SystemClock();

        $walletRepository = app()->make(WalletRepository::class);
        for ($i = 0; $i < 1000; $i++) {
            $walletId = $walletIds[array_rand($walletIds)];
            $amount = random_int(1, 1000);
            $wallet = $walletRepository->retrieve($walletId);
            try {
                $wallet->deposit($amount, 'test', $clock->now());
            } catch (SorryCantWithdraw $exception) {
            } finally {
                $walletRepository->persist($wallet);
            }
        }

        for ($i = 0; $i < 3000; $i++) {
            $walletId = $walletIds[array_rand($walletIds)];
            $amount = random_int(1, 1000);
            /** @var Wallet $wallet */
            $wallet = $walletRepository->retrieve($walletId);
            try {
                if(random_int(1,2) === 1){
                    $wallet->deposit($amount, 'test', $clock->now());
                } else {
                    $wallet->withdraw($amount, 'test', $clock->now());
                }
            } catch (SorryCantWithdraw $exception) {
            } finally {
                $walletRepository->persist($wallet);
            }
        }


    }
}
