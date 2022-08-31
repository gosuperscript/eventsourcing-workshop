<?php

namespace Workshop\Domains\Accounts\Tests\Infra;

use Tests\TestCase;
use Workshop\Domains\Accounts\Account;
use Workshop\Domains\Accounts\Commands\OpenAccount;
use Workshop\Domains\Accounts\Events\AccountOpened;
use Workshop\Domains\Accounts\Events\AccountVerified;
use Workshop\Domains\Accounts\Exceptions\CantPersistAccount;
use Workshop\Domains\Accounts\Infra\AccountRepository;

abstract class AccountRepositoryTestCase extends TestCase
{
    protected AccountRepository $repository;

    abstract public function getRepository(): AccountRepository;

    abstract public function assertDispatched(object $event);

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->getRepository();
    }

    /** @test */
    public function it_can_persist_a_new_aggregate_and_retrieve_its_read_model()
    {
        $command = new OpenAccount('account_id', 'Robert@baelde.nl', 'robert');
        $account = Account::open($command);
        $this->repository->persist($account);

        $this->assertDispatched(AccountOpened::fromCommand($command));

        $this->assertEquals(
            new \Workshop\Domains\Accounts\Query\Account('account_id', 'robert', 'Robert@baelde.nl', false),
            $this->repository->getForRead('account_id')
        );
    }

    /** @test */
    public function it_can_update()
    {
        $command = new OpenAccount('account_id', 'Robert@baelde.nl', 'robert');
        $account = Account::open($command);
        $this->repository->persist($account);

        $account = $this->repository->getAggregate('account_id');
        $account->verify();
        $this->repository->persist($account);

        $this->assertDispatched(new AccountVerified('account_id'));
        $model = $this->repository->getForRead('account_id');
        $this->assertTrue($model->verified);
    }

    /** @test */
    public function it_uses_optimistic_locking()
    {
        $command = new OpenAccount('account_id', 'Robert@baelde.nl', 'robert');
        $account = Account::open($command);
        $this->repository->persist($account);

        $account = $this->repository->getAggregate('account_id');
        $otherAccount = $this->repository->getAggregate('account_id');
        $account->verify();
        // should work
        $this->repository->persist($account);

        $this->expectExceptionObject(CantPersistAccount::accountChangedByOtherProcess());
        $this->repository->persist($otherAccount);
    }
}
