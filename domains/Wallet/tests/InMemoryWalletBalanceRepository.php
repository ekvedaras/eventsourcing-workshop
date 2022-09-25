<?php

namespace Workshop\Domains\Wallet\Tests;

use Workshop\Domains\Wallet\Infra\WalletBalanceRepository;
use Workshop\Domains\Wallet\WalletId;

class InMemoryWalletBalanceRepository implements WalletBalanceRepository
{
    /** @var array<string, int> */
    private array $balance = [];

    public function incrementBalance(WalletId $walletId, int $tokens): void
    {
        $this->balance[$walletId->toString()] ??= 0;
        $this->balance[$walletId->toString()] += $tokens;
    }

    public function decrementBalance(WalletId $walletId, int $tokens): void
    {
        $this->balance[$walletId->toString()] -= $tokens;
    }

    public function getBalance(WalletId $walletId): int
    {
        if (!isset($this->balance[$walletId->toString()])) {
            throw new \RuntimeException("Wallet with ID {$walletId->toString()} does not exist");
        }

        return $this->balance[$walletId->toString()];
    }
}