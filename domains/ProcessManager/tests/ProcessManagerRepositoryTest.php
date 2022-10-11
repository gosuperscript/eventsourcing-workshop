<?php

namespace Workshop\Domains\ProcessManager\Tests;

use EventSauce\EventSourcing\Message;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Workshop\Domains\ProcessManager\IlluminateProcessManagerRepository;
use Workshop\Domains\ProcessManager\ProcessManager;

class ProcessManagerRepositoryTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_return_all_configured_process_managers()
    {
        $repo = new IlluminateProcessManagerRepository(
            tableName: 'process_managers',
            processManagers: [
            'testProcessManager' => new TestProcessManager(),
        ]);
        $managers = $repo->getProcessManagers();
        $this->assertCount(1, $managers);
    }

    /** @test */
    public function it_can_persist_and_return_process_managers()
    {
        $repo = new IlluminateProcessManagerRepository(
            tableName: 'process_managers',
            processManagers: [
            'testProcessManager' => new TestProcessManager(),
        ]);

        $manager = $repo->getProcessManagers()[0];
        $manager->handle(new Message(new class(){}));
        $repo->persist($manager);

        $this->assertEquals($manager, $repo->hasProcessManagerForId($manager->getProcessId()));



    }
}

class TestProcessManager implements ProcessManager
{

    private int $handledCount = 0;
    private $processId = 'foo';

    public function handle(Message $message): void
    {
        $this->handledCount += 1;
    }

    public function startsOn(Message $message): bool
    {
        return true;
    }

    public function toPayload(): array
    {
        return [
            'handledCount' => $this->handledCount,
        ];
    }

    public function fromPayload(array $payload): void
    {
        $this->handledCount = $payload['handledCount'] ?? 0;
    }

    public function getHandledCount(): int
    {
        return $this->handledCount;
    }

    public function getProcessId(): string
    {
        return $this->processId;
    }
}
