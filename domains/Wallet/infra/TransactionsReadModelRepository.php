<?php

namespace Workshop\Domains\Wallet\Infra;

use Carbon\Carbon;

interface TransactionsReadModelRepository
{
    public function addTransaction(
        string $eventId,
        string $walletId,
        int $amount,
        Carbon $transactedAt,
        string $description
    ): void;

    public function flush(): void;
}
