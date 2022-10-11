<?php

namespace Workshop\Domains\ProcessManager;

use Assert\Assert;

class IlluminateProcessManagerRepository implements ProcessManagerRepository
{

    /**
     * @param string $tableName
     * @param array<ProcessManager> $processManagers
     */
    public function __construct(
        private string $tableName,
        private array $processManagers
    )
    {
        Assert::thatAll($this->processManagers)->isInstanceOf(ProcessManager::class);
        // assert array named keys
        foreach ($this->processManagers as $key => $processManager) {
            Assert::that($key)->string();
        }
    }

    public function getProcessManagers(): array
    {
        return array_values($this->processManagers);
    }

    public function hasProcessManagerForId(string $correlationId): ?ProcessManager
    {
        $processManager = \DB::table($this->tableName)->where('process_id', $correlationId)->first();
        if ($processManager === null) {
            return null;
        }

        $manager = $this->processManagers[$processManager->type] ?? null;
        if ($manager === null) {
            return null;
        }

        $manager->fromPayload(json_decode($processManager->payload, true));
        return $manager;
    }

    public function persist(ProcessManager $processManager): void
    {
        \DB::table($this->tableName)->updateOrInsert(
            ['process_id' => $processManager->getProcessId()],
            ['payload' => json_encode($processManager->toPayload()), 'type' => $this->getTypeOfProcessManager($processManager)]
        );
    }

    private function getTypeOfProcessManager(ProcessManager $processManager): string
    {
        foreach ($this->processManagers as $processManagerName => $processManagerInstance){
            if ($processManager instanceof $processManagerInstance) {
                return $processManagerName;
            }
        }
        throw new \Exception('ProcessManager not found');
    }


}
