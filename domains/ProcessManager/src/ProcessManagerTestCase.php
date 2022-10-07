<?php

namespace Workshop\Domains\ProcessManager;

use EventSauce\EventSourcing\Message;
use PHPUnit\Framework\TestCase;

abstract class ProcessManagerTestCase extends TestCase
{
    protected ProcessManager $processManager;

    public function setUp(): void
    {
        $this->processManager = $this->processManager();
        parent::setUp();
    }

    protected abstract function processManager(): ProcessManager;

    protected function given(Message ...$messages): self
    {
        foreach ($messages as $message){
            $this->processManager->handle($message);
        }
        if(count($messages) > 0){
            $this->reloadProcessManager();
        }
        return $this;
    }

    protected function when(Message ...$messages): self
    {
        foreach ($messages as $message){
            $this->processManager->handle($message);
        }
        if(count($messages) > 0){
            $this->reloadProcessManager();
        }
        return $this;
    }

    protected function then(\Closure $param)
    {
        $param();
    }

    protected function reloadProcessManager()
    {
        $processManager = $this->processManager();
        $processManager->fromPayload($this->processManager->toPayload());
        $this->processManager = $processManager;
    }
}
