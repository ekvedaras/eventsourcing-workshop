<?php

namespace Workshop\Domains\Wallet\Events;

use EventSauce\EventSourcing\Serialization\SerializablePayload;

final class FailedToWithdrawDueToInsufficientTokens/* implements SerializablePayload*/
{
    public function __construct(
        public readonly int $attempted,
        public readonly int $balance,
    )
    {
    }
//
//    public function toPayload(): array
//    {
//        return [
//            'attempted' => $this->attempted,
//            'balance' => $this->balance,
//        ];
//    }
//
//    public static function fromPayload(array $payload): static
//    {
//        return new self(
//            attempted: $payload['attempted'],
//            balance: $payload['balance'],
//        );
//    }
}