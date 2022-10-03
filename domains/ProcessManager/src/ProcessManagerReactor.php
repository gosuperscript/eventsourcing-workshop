<?php

namespace Workshop\Domains\ProcessManager;

use Assert\Assert;
use EventSauce\EventSourcing\Message;
use EventSauce\EventSourcing\MessageConsumer;

class ProcessManagerReactor implements MessageConsumer
{
    public function __construct(
        private ProcessManagerRepository $processManagerRepository,
        private array $processManagers
    )
    {
        Assert::thatAll($this->processManagers)->isInstanceOf(ProcessManager::class);
    }

    public function handle(Message $message): void
    {
        $correlationId = $message->header(RelationHeaders::CORRELATION_ID);
        if($correlationId === null){
            return;
        }

        $processManager = $this->processManagerRepository->hasProcessManagerForId($correlationId);
        if($processManager !== null){
            $processManager->handle($message);
            $this->processManagerRepository->persist($processManager);
            return;
        }

        /** @var ProcessManager $processManager */
        foreach ($this->processManagers as $processManager){
            if(!$processManager->startsOn($message)){
               continue;
            }
            $processManager->handle($message);
            $this->processManagerRepository->persist($processManager);
            break;
        }
    }
}
