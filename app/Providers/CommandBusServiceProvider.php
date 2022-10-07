<?php

namespace App\Providers;

use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use League\Tactician\CommandBus;
use League\Tactician\Handler\CommandHandlerMiddleware;
use League\Tactician\Handler\CommandNameExtractor\ClassNameExtractor;
use League\Tactician\Handler\Locator\InMemoryLocator;
use League\Tactician\Handler\MethodNameInflector\HandleClassNameInflector;

class CommandBusServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(InMemoryLocator::class, function (Application $application){
            return new InMemoryLocator();
        });

        $this->app->bind(CommandBus::class, function (Application $application){

            $commandHandlerMiddleware = new CommandHandlerMiddleware(
                new ClassNameExtractor(),
                $application->make(InMemoryLocator::class),
                new HandleClassNameInflector()
            );

            return new CommandBus([
                $commandHandlerMiddleware
            ]);
        });
    }
}
