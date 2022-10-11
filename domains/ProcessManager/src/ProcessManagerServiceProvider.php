<?php

namespace Workshop\Domains\ProcessManager;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Workshop\Domains\Wallet\Transactions\DefaultTransactionProcessManager;
use Workshop\Domains\Wallet\Transactions\HighValueTransactionProcessManager;

class ProcessManagerServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(ProcessManagerRepository::class, function (Application $application) {
            return new IlluminateProcessManagerRepository('process_managers', [
                'high_value_transaction_process_manager' => $application->make(HighValueTransactionProcessManager::class),
                'default_transaction_process_manager' => $application->make(DefaultTransactionProcessManager::class)
            ]);
        });
    }
}
