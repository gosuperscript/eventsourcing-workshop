<?php

namespace App;

use Assert\Assert;
use EventSauce\EventSourcing\AggregateRootId;

abstract class Uuid implements AggregateRootId
{
    public function __construct(
        public readonly string $id
    )
    {
        Assert::that($id)->uuid();
    }

    public static function generate(): static
    {
        return new static(\Ramsey\Uuid\Uuid::uuid4()->toString());
    }


    public function toString(): string
    {
        return $this->id;
    }

    public static function fromString(string $aggregateRootId): static
    {
        return new static($aggregateRootId);
    }
}
