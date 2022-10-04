<?php

namespace Workshop\Domains\ProcessManager;

interface HasProcessIds
{
    public function getCorrelationId(): ?string;

    public function getCausationId(): ?string;
}
