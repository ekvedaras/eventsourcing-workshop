<?php

namespace Workshop\Domains\Wallet\Infra;

use Workshop\Domains\Wallet\WalletId;

interface WalletBalanceRepository
{
    public function incrementBalance(WalletId $walletId, int $tokens): void;

    public function decrementBalance(WalletId $walletId, int $tokens): void;

    public function getBalance(WalletId $walletId): int;
}