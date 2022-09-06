<?php

namespace Workshop\Domains\Wallet\Events;

use EventSauce\EventSourcing\Serialization\SerializablePayload;

final class TokensDeposited implements SerializablePayload
{
    public function __construct(public readonly int $tokens)
    {
    }

    public function toPayload(): array
    {
        return ['tokens' => $this->tokens];
    }

    public static function fromPayload(array $payload): static
    {
        return new self($payload['tokens']);
    }
}