<?php

namespace Workshop\Domains\Wallet\Infra;

use Carbon\Carbon;
use Workshop\Domains\Wallet\ReadModels\Transaction;

class EloquentTransactionsReadModelRepository implements TransactionsReadModelRepository
{

    public function addTransaction(string $eventId, string $walletId, int $amount, Carbon $transactedAt, string $description): void
    {
        Transaction::create([
            'event_id' => $eventId,
            'wallet_id' => $walletId,
            'amount' => $amount,
            'transacted_at' => $transactedAt,
            'description' => $description,
        ]);
    }

    public function flush(): void
    {
        Transaction::truncate();
    }
}
