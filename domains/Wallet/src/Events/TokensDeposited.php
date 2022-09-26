<?php

namespace Workshop\Domains\Wallet\Events;

use Carbon\CarbonImmutable;
use DateTimeImmutable;
use EventSauce\EventSourcing\Serialization\SerializablePayload;

final class TokensDeposited implements SerializablePayload
{
    public function __construct(
        public readonly int $tokens,
        public readonly string $description,
        public readonly DateTimeImmutable $transactedAt,
    )
    {
    }

    public function toPayload(): array
    {
        return [
            'tokens' => $this->tokens,
            'description' => $this->description,
            'transacted_at' => $this->transactedAt->format('Y-m-d H:i:s.uO')
        ];
    }

    public static function fromPayload(array $payload): static
    {
        return new self(
            tokens: $payload['tokens'],
            description: $payload['description'] ?? 'unknown',
            transactedAt: CarbonImmutable::parse($payload['transacted_at']),
        );
    }
}