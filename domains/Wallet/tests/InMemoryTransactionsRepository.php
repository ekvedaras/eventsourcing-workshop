<?php

namespace Workshop\Domains\Wallet\Tests;

use Carbon\Carbon;
use Workshop\Domains\Wallet\Infra\TransactionsReadModelRepository;

class InMemoryTransactionsRepository implements TransactionsReadModelRepository
{
    private array $transactions = [];

    public function addTransaction(string $eventId, string $walletId, int $amount, Carbon $transactedAt, string $description): void
    {
        $this->transactions[] = [
            'eventId' => $eventId,
            'walletId' => $walletId,
            'amount' => $amount,
            'transactedAt' => $transactedAt,
            'description' => $description,
        ];
    }

    public function getTransactions(): array
    {
        return $this->transactions;
    }

    public function flush(): void
    {
        $this->transactions = [];
    }
}
