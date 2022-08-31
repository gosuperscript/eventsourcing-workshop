<?php

namespace Workshop\Domains\Accounts\Tests\Infra;

use Illuminate\Database\DatabaseManager;
use Illuminate\Events\NullDispatcher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Testing\Fakes\EventFake;
use Workshop\Domains\Accounts\Infra\AccountRepository;
use Workshop\Domains\Accounts\Infra\IlluminateAccountRepository;

class IlluminateAccountRepositoryTest extends AccountRepositoryTestCase
{
    use RefreshDatabase;

    private EventFake $dispatcher;

    public function eventDispatcher(): EventFake
    {
        if (!isset($this->dispatcher)) {
            $this->dispatcher = new EventFake(new NullDispatcher(new \Illuminate\Events\Dispatcher()));
        }
        return $this->dispatcher;
    }

    public function getRepository(): AccountRepository
    {
        return new IlluminateAccountRepository(
            app()->make(DatabaseManager::class)->connection(),
            'accounts',
            $this->eventDispatcher(),
        );
    }

    public function assertDispatched(object $event)
    {
        $this->eventDispatcher()->assertDispatched(get_class($event));
    }
}
