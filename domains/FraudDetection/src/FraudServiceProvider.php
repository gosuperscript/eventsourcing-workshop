<?php

namespace Workshop\Domains\FraudDetection;

use Illuminate\Support\ServiceProvider;
use League\Tactician\Handler\Locator\InMemoryLocator;
use Workshop\Domains\FraudDetection\Commands\ApproveTransaction;
use Workshop\Domains\FraudDetection\Commands\RejectTransaction;
use Workshop\Domains\FraudDetection\Commands\RequestFraudDetection;

class FraudServiceProvider extends ServiceProvider
{
    public function register()
    {
        /** @var InMemoryLocator $locator */
        $locator = $this->app->make(InMemoryLocator::class);
        $locator->addHandler($this->app->make(FraudCommandHandler::class), RequestFraudDetection::class);
        $locator->addHandler($this->app->make(FraudCommandHandler::class), ApproveTransaction::class);
        $locator->addHandler($this->app->make(FraudCommandHandler::class), RejectTransaction::class);
    }
}
