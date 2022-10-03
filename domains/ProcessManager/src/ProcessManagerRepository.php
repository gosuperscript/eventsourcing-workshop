<?php

namespace Workshop\Domains\ProcessManager;

interface ProcessManagerRepository
{
    public function hasProcessManagerForId(string $correlationId): ?ProcessManager;

    public function persist(ProcessManager $processManager): void;
}
