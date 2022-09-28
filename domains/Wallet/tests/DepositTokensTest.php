<?php

namespace Workshop\Domains\Wallet\Tests;

use Workshop\Domains\Wallet\Events\TokensDeposited;
use Workshop\Domains\Wallet\Wallet;

class DepositTokensTest extends WalletTestCase
{
    public function test_it_can_deposit_tokens(): void
    {
        $this->given()
            ->when(fn (Wallet $wallet) => $wallet->deposit(100, 'demo', $this->clock()))
            ->then(new TokensDeposited(100, 'demo', $this->currentTime()));
    }
}