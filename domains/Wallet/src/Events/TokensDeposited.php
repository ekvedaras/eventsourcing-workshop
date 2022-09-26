<?php

namespace Workshop\Domains\Wallet\Events;

use EventSauce\EventSourcing\Serialization\SerializablePayload;

final class TokensDeposited implements SerializablePayload
{
    public function __construct(
        public readonly int $tokens,
        public readonly string $description,
    )
    {
    }

    public function toPayload(): array
    {
        return [
            'tokens' => $this->tokens,
            'description' => $this->description,
        ];
    }

    public static function fromPayload(array $payload): static
    {
        return new self($payload['tokens'], $payload['description'] ?? 'unknown');
    }
}