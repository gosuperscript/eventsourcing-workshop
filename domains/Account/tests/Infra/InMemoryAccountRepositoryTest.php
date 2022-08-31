<?php

namespace Workshop\Domains\Accounts\Tests\Infra;

use Workshop\Domains\Accounts\Infra\AccountRepository;
use Workshop\Domains\Accounts\Infra\InMemoryAccountRepository;

class InMemoryAccountRepositoryTest extends AccountRepositoryTestCase
{
    public function getRepository(): AccountRepository
    {
        return new InMemoryAccountRepository();
    }

    public function assertDispatched(object $event)
    {
        /** @noinspection PhpPossiblePolymorphicInvocationInspection */
        $this->assertTrue(in_array($event, $this->repository->dispatchedEvents()));
    }
}
