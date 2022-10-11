<?php

namespace Workshop\Domains\ProcessManager;

use Assert\Assert;
use EventSauce\EventSourcing\Message;
use EventSauce\EventSourcing\MessageConsumer;

class ProcessManagerReactor implements MessageConsumer
{
    public function __construct(
        private ProcessManagerRepository $processManagerRepository,
    )
    {
    }

    public function handle(Message $message): void
    {
        $correlationId = $message->header(ProcessHeaders::CORRELATION_ID);
        if($correlationId === null){
            return;
        }

        $processManager = $this->processManagerRepository->hasProcessManagerForId($correlationId);
        if($processManager !== null){
            $processManager->handle($message);
            $this->processManagerRepository->persist($processManager);
            $processManager->releaseCommands();
            return;
        }

        /** @var ProcessManager $processManager */
        foreach ($this->processManagerRepository->getProcessManagers() as $processManager){
            if(!$processManager->startsOn($message)){
               continue;
            }
            $processManager->handle($message);
            $this->processManagerRepository->persist($processManager);
            $processManager->releaseCommands();
            break;
        }
    }
}
