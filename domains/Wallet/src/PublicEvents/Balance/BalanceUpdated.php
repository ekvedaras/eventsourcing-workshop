<?php

namespace Workshop\Domains\Wallet\PublicEvents\Balance;

use Robertbaelde\PersistingMessageBus\PublicMessage;

class BalanceUpdated implements PublicMessage
{
    public function __construct(public readonly int $tokens)
    {
    }

    public function toPayload(): array
    {
        return [
            'tokens' => $this->tokens,
        ];
    }

    public static function fromPayload(array $payload): static
    {
        return new self(
            tokens: $payload['tokens'],
        );
    }
}