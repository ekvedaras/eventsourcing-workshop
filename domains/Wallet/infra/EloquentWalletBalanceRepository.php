<?php

namespace Workshop\Domains\Wallet\Infra;

use Illuminate\Support\Facades\DB;
use Workshop\Domains\Wallet\WalletId;

class EloquentWalletBalanceRepository implements WalletBalanceRepository
{
    public function incrementBalance(WalletId $walletId, int $tokens): void
    {
        $targetQuery = DB::table('wallet_balance')->where('wallet_id', $walletId->toString());

        if ($targetQuery->doesntExist()) {
            $targetQuery->insert(['wallet_id' => $walletId->toString(), 'tokens' => $tokens]);
        } else {
            $targetQuery->update(['tokens' => DB::raw("tokens + $tokens")]);
        }
    }

    public function decrementBalance(WalletId $walletId, int $tokens): void
    {
        DB::table('wallet_balance')->where('wallet_id', $walletId->toString())->update(['tokens' => DB::raw("tokens - $tokens")]);
    }

    public function getBalance(WalletId $walletId): int
    {
        $balance = DB::table('wallet_balance')->where('wallet_id', $walletId->toString())->value('tokens');

        if (is_null($balance)) {
            throw new \RuntimeException("Wallet with ID {$walletId->toString()} does not exist");
        }

        return (int) $balance;
    }
}